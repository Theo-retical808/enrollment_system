<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Petition;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class IrregularStudentEnrollmentService
{
    protected $paymentService;

    public function __construct(PaymentVerificationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create manual enrollment for irregular student.
     */
    public function createManualEnrollment(Student $student): Enrollment
    {
        // Check if student already has an enrollment for current semester
        $currentSemester = $this->paymentService->getCurrentSemester();
        $currentAcademicYear = $this->paymentService->getCurrentAcademicYear();
        
        $existingEnrollment = $student->enrollments()
            ->where('semester', $currentSemester)
            ->where('academic_year', $currentAcademicYear)
            ->first();
        
        if ($existingEnrollment) {
            return $existingEnrollment;
        }

        // Create new enrollment
        return Enrollment::create([
            'student_id' => $student->id,
            'semester' => $currentSemester,
            'academic_year' => $currentAcademicYear,
            'status' => 'draft',
            'total_units' => 0,
        ]);
    }

    /**
     * Get available courses for selection.
     */
    public function getAvailableCourses(Student $student, string $search = null): Collection
    {
        $query = Course::where('is_active', true)
            ->with(['school', 'prerequisites']);

        // Filter by search term
        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Get courses not already enrolled in current enrollment
        $currentEnrollment = $this->getStudentEnrollment($student);
        if ($currentEnrollment) {
            $enrolledCourseIds = $currentEnrollment->courses()->pluck('courses.id')->toArray();
            if (!empty($enrolledCourseIds)) {
                $query->whereNotIn('id', $enrolledCourseIds);
            }
        }

        return $query->orderBy('course_code')->get();
    }

    /**
     * Add course to enrollment.
     */
    public function addCourseToEnrollment(Enrollment $enrollment, Course $course, array $scheduleData): array
    {
        // Validate prerequisites
        if (!$course->hasPrerequisitesSatisfiedBy($enrollment->student)) {
            return [
                'success' => false,
                'message' => 'Prerequisites not satisfied for this course.',
                'missing_prerequisites' => $this->getMissingPrerequisites($enrollment->student, $course),
            ];
        }

        // Check unit load limit (21 units max)
        $currentUnits = $enrollment->getTotalUnits();
        if ($currentUnits + $course->units > 21) {
            return [
                'success' => false,
                'message' => "Adding this course would exceed the 21-unit limit. Current: {$currentUnits} units, Course: {$course->units} units.",
            ];
        }

        // Check schedule conflicts
        if ($this->hasScheduleConflict($enrollment, $scheduleData)) {
            return [
                'success' => false,
                'message' => 'This course conflicts with your existing schedule.',
                'conflicting_courses' => $this->getConflictingCourses($enrollment, $scheduleData),
            ];
        }

        // Add course to enrollment
        $enrollment->courses()->attach($course->id, [
            'schedule_day' => $scheduleData['schedule_day'],
            'start_time' => $scheduleData['start_time'],
            'end_time' => $scheduleData['end_time'],
            'room' => $scheduleData['room'] ?? 'TBA',
            'instructor' => $scheduleData['instructor'] ?? 'TBA',
        ]);

        // Update total units
        $enrollment->update(['total_units' => $enrollment->getTotalUnits()]);

        return [
            'success' => true,
            'message' => 'Course added successfully.',
            'new_total_units' => $enrollment->total_units,
        ];
    }

    /**
     * Remove course from enrollment.
     */
    public function removeCourseFromEnrollment(Enrollment $enrollment, Course $course): array
    {
        $enrollment->courses()->detach($course->id);
        $enrollment->update(['total_units' => $enrollment->getTotalUnits()]);

        return [
            'success' => true,
            'message' => 'Course removed successfully.',
            'new_total_units' => $enrollment->total_units,
        ];
    }

    /**
     * Get missing prerequisites for a course.
     */
    protected function getMissingPrerequisites(Student $student, Course $course): Collection
    {
        $prerequisites = $course->prerequisites;
        $completedCourseIds = $student->completedCourses()
            ->wherePivot('passed', true)
            ->pluck('courses.id')
            ->toArray();

        return $prerequisites->filter(function ($prerequisite) use ($completedCourseIds) {
            return !in_array($prerequisite->id, $completedCourseIds);
        });
    }

    /**
     * Check if schedule conflicts with existing courses.
     */
    protected function hasScheduleConflict(Enrollment $enrollment, array $scheduleData): bool
    {
        foreach ($enrollment->courses as $existingCourse) {
            if ($existingCourse->pivot->schedule_day === $scheduleData['schedule_day']) {
                $existingStart = strtotime($existingCourse->pivot->start_time);
                $existingEnd = strtotime($existingCourse->pivot->end_time);
                $newStart = strtotime($scheduleData['start_time']);
                $newEnd = strtotime($scheduleData['end_time']);

                // Check for overlap
                if ($existingStart < $newEnd && $newStart < $existingEnd) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get conflicting courses.
     */
    protected function getConflictingCourses(Enrollment $enrollment, array $scheduleData): Collection
    {
        return $enrollment->courses->filter(function ($course) use ($scheduleData) {
            if ($course->pivot->schedule_day === $scheduleData['schedule_day']) {
                $existingStart = strtotime($course->pivot->start_time);
                $existingEnd = strtotime($course->pivot->end_time);
                $newStart = strtotime($scheduleData['start_time']);
                $newEnd = strtotime($scheduleData['end_time']);

                return $existingStart < $newEnd && $newStart < $existingEnd;
            }
            return false;
        });
    }

    /**
     * Get enrollment for student.
     */
    public function getStudentEnrollment(Student $student): ?Enrollment
    {
        $currentSemester = $this->paymentService->getCurrentSemester();
        $currentAcademicYear = $this->paymentService->getCurrentAcademicYear();
        
        return $student->enrollments()
            ->where('semester', $currentSemester)
            ->where('academic_year', $currentAcademicYear)
            ->first();
    }

    /**
     * Submit enrollment for approval.
     */
    public function submitForApproval(Enrollment $enrollment): bool
    {
        if ($enrollment->status !== 'draft') {
            return false;
        }

        if ($enrollment->courses()->count() === 0) {
            return false;
        }

        // Assign professor for review
        $professor = $enrollment->student->school->professors()->where('status', 'active')->first();
        
        $enrollment->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'professor_id' => $professor?->id,
        ]);

        return true;
    }

    /**
     * Create petition for failed course.
     */
    public function createPetition(Student $student, Course $course, string $justification): Petition
    {
        return Petition::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'justification' => $justification,
            'status' => 'pending',
        ]);
    }

    /**
     * Get student's petitions.
     */
    public function getStudentPetitions(Student $student): Collection
    {
        return $student->petitions()->with(['course', 'reviewer'])->latest()->get();
    }

    /**
     * Get failed courses that can be petitioned.
     */
    public function getFailedCourses(Student $student): Collection
    {
        return $student->completedCourses()
            ->wherePivot('passed', false)
            ->get();
    }

    /**
     * Get course schedule options (mock data for now).
     */
    public function getCourseScheduleOptions(Course $course): array
    {
        // In a real system, this would come from a course_schedules table
        // For now, return mock schedule options
        return [
            [
                'schedule_day' => 'Monday',
                'start_time' => '08:00',
                'end_time' => '10:00',
                'room' => 'Room 101',
                'instructor' => 'Prof. Smith',
                'available_slots' => 30,
            ],
            [
                'schedule_day' => 'Wednesday',
                'start_time' => '14:00',
                'end_time' => '16:00',
                'room' => 'Room 102',
                'instructor' => 'Prof. Johnson',
                'available_slots' => 25,
            ],
            [
                'schedule_day' => 'Friday',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'room' => 'Room 103',
                'instructor' => 'Prof. Davis',
                'available_slots' => 20,
            ],
        ];
    }
}