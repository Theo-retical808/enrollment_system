<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\CurriculumTemplate;
use App\Models\CourseSchedule;
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
        $currentSemester = $this->paymentService->getCurrentSemester();
        $currentAcademicYear = $this->paymentService->getCurrentAcademicYear();

        // Check if student already has an enrollment for current semester
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

        // Assign schedule from curriculum templates
        $this->assignFromCurriculumTemplate($enrollment);

        return $enrollment;
    }

    /**
     * Assign schedule from curriculum_templates table.
     * Falls back to hardcoded templates if no curriculum is defined.
     */
    protected function assignFromCurriculumTemplate(Enrollment $enrollment): void
    {
        $student = $enrollment->student;
        $currentSemester = $this->paymentService->getCurrentSemester();

        // Try to get curriculum template entries for this student's school/year/semester
        $templates = CurriculumTemplate::where('school_id', $student->school_id)
            ->where('year_level', $student->year_level)
            ->where('semester', $currentSemester)
            ->where('is_active', true)
            ->with(['course', 'courseSchedule.professor'])
            ->get();

        if ($templates->isNotEmpty()) {
            // Use curriculum templates
            foreach ($templates as $template) {
                $schedule = $template->courseSchedule;

                if ($schedule) {
                    // Use the linked schedule
                    $enrollment->courses()->attach($template->course_id, [
                        'schedule_day' => $schedule->day,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'room' => $schedule->room,
                        'instructor' => $schedule->professor->full_name,
                    ]);
                } else {
                    // No specific schedule linked — find any available schedule for this course
                    $availableSchedule = CourseSchedule::where('course_id', $template->course_id)
                        ->where('is_active', true)
                        ->with('professor')
                        ->first();

                    if ($availableSchedule) {
                        $enrollment->courses()->attach($template->course_id, [
                            'schedule_day' => $availableSchedule->day,
                            'start_time' => $availableSchedule->start_time,
                            'end_time' => $availableSchedule->end_time,
                            'room' => $availableSchedule->room,
                            'instructor' => $availableSchedule->professor->full_name,
                        ]);
                    } else {
                        // No schedule at all — attach with TBA
                        $enrollment->courses()->attach($template->course_id, [
                            'schedule_day' => 'TBA',
                            'start_time' => '00:00',
                            'end_time' => '00:00',
                            'room' => 'TBA',
                            'instructor' => 'TBA',
                        ]);
                    }
                }
            }
        } else {
            // Fallback: use hardcoded schedule templates
            $this->assignFallbackSchedule($enrollment);
        }

        // Update total units
        $totalUnits = $enrollment->courses()->sum('units');
        $enrollment->update(['total_units' => $totalUnits]);
    }

    /**
     * Fallback: assign schedule from hardcoded templates when no curriculum is defined.
     */
    protected function assignFallbackSchedule(Enrollment $enrollment): void
    {
        $student = $enrollment->student;
        $schoolCode = $student->school->code ?? 'CS';
        $yearLevel = $student->year_level;

        $templates = [
            'CS' => [
                1 => [
                    ['course_code' => 'CS101', 'schedule_day' => 'Monday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'CS-101', 'instructor' => 'Prof. Smith'],
                    ['course_code' => 'MATH101', 'schedule_day' => 'Tuesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'AS-401', 'instructor' => 'Prof. Wilson'],
                    ['course_code' => 'ENGL101', 'schedule_day' => 'Monday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'ENG-205', 'instructor' => 'Prof. Martinez'],
                    ['course_code' => 'PE101', 'schedule_day' => 'Friday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'Gym A', 'instructor' => 'Prof. Wilson'],
                    ['course_code' => 'HIST101', 'schedule_day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'AS-403', 'instructor' => 'Prof. Wilson'],
                ],
                2 => [
                    ['course_code' => 'CS202', 'schedule_day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'CS-104', 'instructor' => 'Prof. Smith'],
                    ['course_code' => 'MATH201', 'schedule_day' => 'Tuesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'AS-402', 'instructor' => 'Prof. Wilson'],
                    ['course_code' => 'STAT101', 'schedule_day' => 'Thursday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'AS-404', 'instructor' => 'Prof. Wilson'],
                    ['course_code' => 'BUS101', 'schedule_day' => 'Monday', 'start_time' => '13:00', 'end_time' => '14:30', 'room' => 'BUS-301', 'instructor' => 'Prof. Davis'],
                    ['course_code' => 'ACCT101', 'schedule_day' => 'Thursday', 'start_time' => '10:00', 'end_time' => '11:30', 'room' => 'BUS-304', 'instructor' => 'Prof. Davis'],
                ],
                3 => [
                    ['course_code' => 'CS301', 'schedule_day' => 'Tuesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'CS-103', 'instructor' => 'Prof. Smith'],
                    ['course_code' => 'CS302', 'schedule_day' => 'Monday', 'start_time' => '13:00', 'end_time' => '14:30', 'room' => 'CS-108', 'instructor' => 'Prof. Garcia'],
                    ['course_code' => 'ECON101', 'schedule_day' => 'Friday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'BUS-305', 'instructor' => 'Prof. Davis'],
                    ['course_code' => 'BUS201', 'schedule_day' => 'Wednesday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'BUS-604', 'instructor' => 'Prof. Davis'],
                    ['course_code' => 'BUS202', 'schedule_day' => 'Thursday', 'start_time' => '08:00', 'end_time' => '09:30', 'room' => 'BUS-605', 'instructor' => 'Prof. Davis'],
                ],
            ],
        ];

        $schoolTemplate = $templates[$schoolCode] ?? $templates['CS'];
        $yearTemplate = $schoolTemplate[$yearLevel] ?? $schoolTemplate[1];

        foreach ($yearTemplate as $courseInfo) {
            $course = Course::where('course_code', $courseInfo['course_code'])->first();
            if ($course) {
                $enrollment->courses()->attach($course->id, [
                    'schedule_day' => $courseInfo['schedule_day'],
                    'start_time' => $courseInfo['start_time'],
                    'end_time' => $courseInfo['end_time'],
                    'room' => $courseInfo['room'],
                    'instructor' => $courseInfo['instructor'],
                ]);
            }
        }
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

        // Assign an enrollment-assistant professor for review (prefer same school)
        $professor = $enrollment->student->school->professors()
            ->where('status', 'active')
            ->where('can_assist_enrollment', true)
            ->first();

        // Fallback: any enrollment assistant
        if (!$professor) {
            $professor = \App\Models\Professor::where('status', 'active')
                ->where('can_assist_enrollment', true)
                ->first();
        }

        // Last fallback: any active professor
        if (!$professor) {
            $professor = \App\Models\Professor::where('status', 'active')->first();
        }

        \Illuminate\Support\Facades\Log::info('Submitting enrollment for approval', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'professor_id' => $professor?->id,
        ]);

        $enrollment->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'professor_id' => $professor?->id,
        ]);

        return true;
    }
}
