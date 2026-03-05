<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\School;
use Illuminate\Support\Collection;

class RegularStudentEnrollmentService
{
    protected $paymentService;

    public function __construct(PaymentVerificationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create automatic schedule assignment for regular student.
     */
    public function createAutomaticEnrollment(Student $student): Enrollment
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
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'semester' => $currentSemester,
            'academic_year' => $currentAcademicYear,
            'status' => 'draft',
            'total_units' => 0,
        ]);

        // Assign predefined schedule based on school and year level
        $this->assignPredefinedSchedule($enrollment);

        return $enrollment;
    }

    /**
     * Assign predefined schedule based on student's school and year level.
     */
    protected function assignPredefinedSchedule(Enrollment $enrollment): void
    {
        $student = $enrollment->student;
        $courses = $this->getPredefinedCourses($student);
        
        foreach ($courses as $courseData) {
            $enrollment->courses()->attach($courseData['course']->id, [
                'schedule_day' => $courseData['schedule_day'],
                'start_time' => $courseData['start_time'],
                'end_time' => $courseData['end_time'],
                'room' => $courseData['room'],
                'instructor' => $courseData['instructor'],
            ]);
        }

        // Update total units
        $totalUnits = $enrollment->courses()->sum('units');
        $enrollment->update(['total_units' => $totalUnits]);
    }

    /**
     * Get predefined courses for a student based on school and year level.
     */
    protected function getPredefinedCourses(Student $student): array
    {
        // This would typically come from a curriculum table
        // For now, we'll create a basic schedule based on school and year level
        
        $schoolCode = $student->school->code ?? 'DEFAULT';
        $yearLevel = $student->year_level;
        
        return $this->getScheduleTemplate($schoolCode, $yearLevel);
    }

    /**
     * Get schedule template based on school and year level.
     */
    protected function getScheduleTemplate(string $schoolCode, int $yearLevel): array
    {
        // Sample schedule templates - in a real system, this would come from database
        $templates = [
            'CS' => [ // Computer Science
                1 => [
                    ['course_code' => 'CS101', 'schedule_day' => 'Monday', 'start_time' => '08:00', 'end_time' => '10:00', 'room' => 'CS-101', 'instructor' => 'Prof. Smith'],
                    ['course_code' => 'MATH101', 'schedule_day' => 'Tuesday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'MATH-201', 'instructor' => 'Prof. Johnson'],
                    ['course_code' => 'ENGL101', 'schedule_day' => 'Wednesday', 'start_time' => '14:00', 'end_time' => '16:00', 'room' => 'ENG-301', 'instructor' => 'Prof. Davis'],
                    ['course_code' => 'PE101', 'schedule_day' => 'Thursday', 'start_time' => '16:00', 'end_time' => '18:00', 'room' => 'GYM-A', 'instructor' => 'Coach Wilson'],
                    ['course_code' => 'HIST101', 'schedule_day' => 'Friday', 'start_time' => '08:00', 'end_time' => '10:00', 'room' => 'HIST-101', 'instructor' => 'Prof. Brown'],
                ],
                2 => [
                    ['course_code' => 'CS201', 'schedule_day' => 'Monday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'CS-102', 'instructor' => 'Prof. Anderson'],
                    ['course_code' => 'MATH201', 'schedule_day' => 'Tuesday', 'start_time' => '08:00', 'end_time' => '10:00', 'room' => 'MATH-202', 'instructor' => 'Prof. Taylor'],
                    ['course_code' => 'PHYS101', 'schedule_day' => 'Wednesday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'PHYS-101', 'instructor' => 'Prof. Miller'],
                    ['course_code' => 'CS202', 'schedule_day' => 'Thursday', 'start_time' => '14:00', 'end_time' => '16:00', 'room' => 'CS-103', 'instructor' => 'Prof. Garcia'],
                    ['course_code' => 'ENGL201', 'schedule_day' => 'Friday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'ENG-302', 'instructor' => 'Prof. Martinez'],
                ],
            ],
            'ENG' => [ // Engineering
                1 => [
                    ['course_code' => 'ENG101', 'schedule_day' => 'Monday', 'start_time' => '08:00', 'end_time' => '10:00', 'room' => 'ENG-101', 'instructor' => 'Prof. Lee'],
                    ['course_code' => 'MATH101', 'schedule_day' => 'Tuesday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'MATH-101', 'instructor' => 'Prof. Kim'],
                    ['course_code' => 'PHYS101', 'schedule_day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '10:00', 'room' => 'PHYS-101', 'instructor' => 'Prof. Chen'],
                    ['course_code' => 'CHEM101', 'schedule_day' => 'Thursday', 'start_time' => '14:00', 'end_time' => '16:00', 'room' => 'CHEM-101', 'instructor' => 'Prof. Wang'],
                    ['course_code' => 'DRAW101', 'schedule_day' => 'Friday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'DRAW-101', 'instructor' => 'Prof. Liu'],
                ],
            ],
            'BUS' => [ // Business
                1 => [
                    ['course_code' => 'BUS101', 'schedule_day' => 'Monday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'BUS-101', 'instructor' => 'Prof. Adams'],
                    ['course_code' => 'ECON101', 'schedule_day' => 'Tuesday', 'start_time' => '14:00', 'end_time' => '16:00', 'room' => 'ECON-101', 'instructor' => 'Prof. Clark'],
                    ['course_code' => 'ACCT101', 'schedule_day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '10:00', 'room' => 'ACCT-101', 'instructor' => 'Prof. Lewis'],
                    ['course_code' => 'STAT101', 'schedule_day' => 'Thursday', 'start_time' => '10:00', 'end_time' => '12:00', 'room' => 'STAT-101', 'instructor' => 'Prof. Hall'],
                    ['course_code' => 'BENGL101', 'schedule_day' => 'Friday', 'start_time' => '14:00', 'end_time' => '16:00', 'room' => 'ENG-201', 'instructor' => 'Prof. Young'],
                ],
            ],
        ];

        $schoolTemplate = $templates[$schoolCode] ?? $templates['CS']; // Default to CS if school not found
        $yearTemplate = $schoolTemplate[$yearLevel] ?? $schoolTemplate[1]; // Default to year 1 if year not found

        // Convert course codes to actual course objects
        $scheduleData = [];
        foreach ($yearTemplate as $courseInfo) {
            $course = Course::where('course_code', $courseInfo['course_code'])->first();
            if ($course) {
                $scheduleData[] = [
                    'course' => $course,
                    'schedule_day' => $courseInfo['schedule_day'],
                    'start_time' => $courseInfo['start_time'],
                    'end_time' => $courseInfo['end_time'],
                    'room' => $courseInfo['room'],
                    'instructor' => $courseInfo['instructor'],
                ];
            }
        }

        return $scheduleData;
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

        // Assign professor for review (simple assignment based on school)
        $professor = $enrollment->student->school->professors()->where('status', 'active')->first();
        
        // If no professor found in the school, get any active professor
        if (!$professor) {
            $professor = \App\Models\Professor::where('status', 'active')->first();
        }
        
        // Log for debugging
        \Log::info('Submitting enrollment for approval', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'professor_id' => $professor?->id,
            'school_id' => $enrollment->student->school_id,
        ]);
        
        $enrollment->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'professor_id' => $professor?->id,
        ]);

        return true;
    }
}