<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ScheduleValidationService
{
    const MAX_UNIT_LOAD = 21;
    const RECOMMENDED_MIN_UNITS = 12;
    const RECOMMENDED_MAX_UNITS = 18;
    const CACHE_TTL = 3600; // 1 hour cache for student data

    /**
     * Validate a complete schedule for a student.
     */
    public function validateSchedule(Student $student, Collection $courses): array
    {
        $validationResults = [
            'is_valid' => true,
            'errors' => [],
            'warnings' => [],
            'unit_load' => 0,
            'prerequisite_violations' => [],
            'schedule_conflicts' => [],
            'unit_limit_exceeded' => false,
        ];

        // Calculate total unit load
        $unitLoad = $this->calculateUnitLoad($courses);
        $validationResults['unit_load'] = $unitLoad;

        // Check unit limit (21 units maximum)
        if ($unitLoad > self::MAX_UNIT_LOAD) {
            $validationResults['is_valid'] = false;
            $validationResults['unit_limit_exceeded'] = true;
            $validationResults['errors'][] = "Unit load ({$unitLoad} units) exceeds the maximum limit of " . self::MAX_UNIT_LOAD . " units.";
        }

        // Validate prerequisites
        $prerequisiteViolations = $this->validatePrerequisites($student, $courses);
        if (!empty($prerequisiteViolations)) {
            $validationResults['is_valid'] = false;
            $validationResults['prerequisite_violations'] = $prerequisiteViolations;
            foreach ($prerequisiteViolations as $violation) {
                $validationResults['errors'][] = $violation['message'];
            }
        }

        // Check for schedule conflicts
        $scheduleConflicts = $this->detectScheduleConflicts($courses);
        if (!empty($scheduleConflicts)) {
            $validationResults['is_valid'] = false;
            $validationResults['schedule_conflicts'] = $scheduleConflicts;
            foreach ($scheduleConflicts as $conflict) {
                $validationResults['errors'][] = $conflict['message'];
            }
        }

        // Add warnings for edge cases
        if ($unitLoad < self::RECOMMENDED_MIN_UNITS) {
            $validationResults['warnings'][] = "Unit load ({$unitLoad} units) is below the recommended minimum of " . self::RECOMMENDED_MIN_UNITS . " units.";
        }

        if ($unitLoad > self::RECOMMENDED_MAX_UNITS && $unitLoad <= self::MAX_UNIT_LOAD) {
            $validationResults['warnings'][] = "Unit load ({$unitLoad} units) is above the recommended maximum of " . self::RECOMMENDED_MAX_UNITS . " units.";
        }

        return $validationResults;
    }

    /**
     * Calculate total unit load for a collection of courses.
     */
    public function calculateUnitLoad(Collection $courses): int
    {
        return $courses->filter(function ($course) {
            // Only count courses with valid unit values
            return isset($course->units) && is_numeric($course->units) && $course->units > 0;
        })->sum('units');
    }

    /**
     * Validate prerequisites for all courses against student's completed courses.
     * This is the core prerequisite validation logic that checks if a student
     * has completed all required prerequisite courses with passing grades.
     * Uses caching to improve performance for repeated validations.
     */
    public function validatePrerequisites(Student $student, Collection $courses): array
    {
        $violations = [];
        
        // Cache student's completed courses to avoid repeated database queries
        $cacheKey = "student_completed_courses_{$student->id}";
        $completedCourseIds = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($student) {
            return $student->completedCourses()
                ->wherePivot('passed', true)
                ->pluck('courses.id')
                ->toArray();
        });

        foreach ($courses as $course) {
            // Skip courses without proper structure
            if (!isset($course->id) || !isset($course->course_code)) {
                continue;
            }

            // Cache course prerequisites to avoid repeated queries
            $prereqCacheKey = "course_prerequisites_{$course->id}";
            $prerequisites = Cache::remember($prereqCacheKey, self::CACHE_TTL, function () use ($course) {
                return $course->prerequisites ?? collect();
            });
            
            foreach ($prerequisites as $prerequisite) {
                if (!in_array($prerequisite->id, $completedCourseIds)) {
                    $violations[] = [
                        'course_id' => $course->id,
                        'course_code' => $course->course_code,
                        'course_title' => $course->title ?? 'Unknown Course',
                        'prerequisite_id' => $prerequisite->id,
                        'prerequisite_code' => $prerequisite->course_code,
                        'prerequisite_title' => $prerequisite->title ?? 'Unknown Prerequisite',
                        'message' => "Cannot enroll in {$course->course_code} ({$course->title}) - missing prerequisite: {$prerequisite->course_code} ({$prerequisite->title})"
                    ];
                }
            }
        }

        return $violations;
    }

    /**
     * Detect schedule conflicts between courses using enrollment pivot data.
     * This method checks for time overlaps on the same day between courses.
     */
    public function detectScheduleConflicts(Collection $courses): array
    {
        $conflicts = [];
        $schedules = [];

        // Build schedule array with time slots from enrollment pivot data
        foreach ($courses as $course) {
            // Skip courses without proper structure or schedule data
            if (!isset($course->id) || !isset($course->course_code) || !$course->pivot) {
                continue; 
            }
            
            $day = $course->pivot->schedule_day ?? null;
            $startTime = $course->pivot->start_time ?? null;
            $endTime = $course->pivot->end_time ?? null;
            
            // Skip courses with incomplete schedule data
            if (!$day || !$startTime || !$endTime) {
                continue;
            }
            
            // Check for conflicts with existing schedules
            foreach ($schedules as $existingSchedule) {
                if ($existingSchedule['day'] === $day) {
                    if ($this->hasTimeOverlap($existingSchedule['start_time'], $existingSchedule['end_time'], $startTime, $endTime)) {
                        $conflicts[] = [
                            'course1' => $existingSchedule,
                            'course2' => [
                                'course_id' => $course->id,
                                'course_code' => $course->course_code,
                                'title' => $course->title ?? 'Unknown Course',
                                'day' => $day,
                                'start_time' => $startTime,
                                'end_time' => $endTime
                            ],
                            'message' => "Schedule conflict: {$existingSchedule['course_code']} and {$course->course_code} both scheduled on {$day} from " . 
                                       $this->formatTime($startTime) . " to " . $this->formatTime($endTime)
                        ];
                    }
                }
            }
            
            // Add current course to schedules array
            $schedules[] = [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'title' => $course->title ?? 'Unknown Course',
                'day' => $day,
                'start_time' => $startTime,
                'end_time' => $endTime
            ];
        }

        return $conflicts;
    }

    /**
     * Check if two time periods overlap.
     */
    public function hasTimeOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        $start1Time = Carbon::parse($start1);
        $end1Time = Carbon::parse($end1);
        $start2Time = Carbon::parse($start2);
        $end2Time = Carbon::parse($end2);
        
        // Two time periods overlap if start1 < end2 AND start2 < end1
        return $start1Time->lt($end2Time) && $start2Time->lt($end1Time);
    }

    /**
     * Format time for display.
     */
    protected function formatTime(string $time): string
    {
        return Carbon::parse($time)->format('g:i A');
    }

    /**
     * Get sample schedule for a course (in real system, this would be from database).
     */
    private function getCourseSchedule(Course $course): array
    {
        // Sample schedules - in real system this would come from course_schedules table
        $sampleSchedules = [
            'CS101' => [
                ['day' => 'Monday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'CS-101', 'instructor' => 'Dr. Smith'],
                ['day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'CS-101', 'instructor' => 'Dr. Smith'],
                ['day' => 'Friday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'CS-101', 'instructor' => 'Dr. Smith'],
            ],
            'CS201' => [
                ['day' => 'Tuesday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'CS-201', 'instructor' => 'Dr. Johnson'],
                ['day' => 'Thursday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'CS-201', 'instructor' => 'Dr. Johnson'],
            ],
            'MATH101' => [
                ['day' => 'Monday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'MATH-101', 'instructor' => 'Prof. Davis'],
                ['day' => 'Wednesday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'MATH-101', 'instructor' => 'Prof. Davis'],
                ['day' => 'Friday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'MATH-101', 'instructor' => 'Prof. Davis'],
            ],
            'ENGL101' => [
                ['day' => 'Tuesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'ENG-101', 'instructor' => 'Prof. Wilson'],
                ['day' => 'Thursday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'ENG-101', 'instructor' => 'Prof. Wilson'],
            ],
        ];

        return $sampleSchedules[$course->course_code] ?? [
            ['day' => 'Monday', 'start_time' => '14:00', 'end_time' => '15:30', 'room' => 'TBA', 'instructor' => 'TBA']
        ];
    }

    /**
     * Validate a single course addition to existing schedule.
     */
    public function validateCourseAddition(Student $student, Course $course, Collection $existingCourses): array
    {
        $allCourses = $existingCourses->push($course);
        return $this->validateSchedule($student, $allCourses);
    }

    /**
     * Get real-time validation feedback for AJAX requests.
     */
    public function getValidationFeedback(Student $student, array $courseIds): array
    {
        $courses = Course::whereIn('id', $courseIds)->get();
        $validation = $this->validateSchedule($student, $courses);

        return [
            'is_valid' => $validation['is_valid'],
            'unit_load' => $validation['unit_load'],
            'remaining_units' => self::MAX_UNIT_LOAD - $validation['unit_load'],
            'errors' => $validation['errors'],
            'warnings' => $validation['warnings'],
            'can_add_more' => $validation['unit_load'] < self::MAX_UNIT_LOAD && $validation['is_valid'],
            'prerequisite_violations' => $validation['prerequisite_violations'],
            'schedule_conflicts' => $validation['schedule_conflicts'],
        ];
    }

    /**
     * Check if a specific course can be added to current schedule.
     */
    public function canAddCourse(Student $student, Course $course, Collection $existingCourses): array
    {
        $validation = $this->validateCourseAddition($student, $course, $existingCourses);
        
        return [
            'can_add' => $validation['is_valid'],
            'reasons' => $validation['errors'],
            'new_unit_load' => $validation['unit_load'],
            'would_exceed_limit' => $validation['unit_limit_exceeded'],
            'prerequisite_violations' => $validation['prerequisite_violations'],
            'schedule_conflicts' => $validation['schedule_conflicts'],
        ];
    }

    /**
     * Check if a specific course with schedule can be added without conflicts.
     */
    public function canAddCourseWithSchedule(Student $student, Course $course, Collection $existingCourses, array $scheduleData): array
    {
        $reasons = [];
        $canAdd = true;

        // Check prerequisites
        $prerequisiteViolations = $this->validatePrerequisites($student, collect([$course]));
        if (!empty($prerequisiteViolations)) {
            $canAdd = false;
            foreach ($prerequisiteViolations as $violation) {
                $reasons[] = $violation['message'];
            }
        }

        // Check unit load
        $currentUnits = $this->calculateUnitLoad($existingCourses);
        $newUnitLoad = $currentUnits + $course->units;
        if ($newUnitLoad > self::MAX_UNIT_LOAD) {
            $canAdd = false;
            $reasons[] = "Adding this course would exceed the " . self::MAX_UNIT_LOAD . "-unit limit. Current: {$currentUnits} units, Course: {$course->units} units.";
        }

        // Check schedule conflicts
        $hasConflict = $this->hasScheduleConflictWithData($existingCourses, $scheduleData);
        if ($hasConflict) {
            $canAdd = false;
            $conflictingCourses = $this->getConflictingCoursesWithData($existingCourses, $scheduleData);
            $reasons[] = "Schedule conflict with: " . $conflictingCourses->pluck('course_code')->join(', ');
        }

        return [
            'can_add' => $canAdd,
            'reasons' => $reasons,
            'new_unit_load' => $newUnitLoad,
            'would_exceed_limit' => $newUnitLoad > self::MAX_UNIT_LOAD,
        ];
    }

    /**
     * Check if schedule conflicts with existing courses using schedule data.
     */
    protected function hasScheduleConflictWithData(Collection $existingCourses, array $scheduleData): bool
    {
        foreach ($existingCourses as $existingCourse) {
            if (!$existingCourse->pivot) {
                continue;
            }
            
            if ($existingCourse->pivot->schedule_day === $scheduleData['schedule_day']) {
                if ($this->hasTimeOverlap(
                    $existingCourse->pivot->start_time,
                    $existingCourse->pivot->end_time,
                    $scheduleData['start_time'],
                    $scheduleData['end_time']
                )) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get conflicting courses using schedule data.
     */
    protected function getConflictingCoursesWithData(Collection $existingCourses, array $scheduleData): Collection
    {
        return $existingCourses->filter(function ($course) use ($scheduleData) {
            if (!$course->pivot) {
                return false;
            }
            
            if ($course->pivot->schedule_day === $scheduleData['schedule_day']) {
                return $this->hasTimeOverlap(
                    $course->pivot->start_time,
                    $course->pivot->end_time,
                    $scheduleData['start_time'],
                    $scheduleData['end_time']
                );
            }
            return false;
        });
    }
}