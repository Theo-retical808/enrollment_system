<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Professor;
use App\Models\Enrollment;
use App\Services\ScheduleValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleSubmissionController extends Controller
{
    protected $validationService;

    public function __construct(ScheduleValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Submit schedule for final validation and professor assignment.
     */
    public function submitSchedule(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollment = $student->getCurrentEnrollment();

        if (!$enrollment || $enrollment->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'No draft enrollment found or enrollment already submitted.'
            ], 400);
        }

        // Get enrolled courses
        $courses = $enrollment->courses;

        // Perform final validation
        $validation = $this->validationService->validateSchedule($student, $courses);

        if (!$validation['is_valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule validation failed. Please fix the following issues:',
                'errors' => $validation['errors'],
                'validation_details' => $validation
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Assign professor based on student's school
            $professor = $this->assignProfessor($student);

            // Update enrollment status and assign professor
            $enrollment->update([
                'status' => 'submitted',
                'professor_id' => $professor->id,
                'submitted_at' => now(),
                'total_units' => $validation['unit_load'],
                'validation_data' => json_encode($validation),
            ]);

            // Log submission activity
            $this->logSubmissionActivity($enrollment, $validation);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Schedule submitted successfully for professor review.',
                'enrollment_id' => $enrollment->id,
                'professor' => [
                    'name' => $professor->full_name,
                    'department' => $professor->school->name,
                ],
                'validation_summary' => [
                    'total_units' => $validation['unit_load'],
                    'course_count' => $courses->count(),
                    'warnings' => $validation['warnings'],
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit schedule. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate schedule before submission (AJAX endpoint).
     */
    public function validateBeforeSubmission(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollment = $student->getCurrentEnrollment();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No enrollment found.'
            ], 404);
        }

        $courses = $enrollment->courses;
        $validation = $this->validationService->validateSchedule($student, $courses);

        return response()->json([
            'success' => true,
            'validation' => $validation,
            'can_submit' => $validation['is_valid'],
            'summary' => [
                'total_courses' => $courses->count(),
                'total_units' => $validation['unit_load'],
                'remaining_units' => 21 - $validation['unit_load'],
                'has_errors' => !empty($validation['errors']),
                'has_warnings' => !empty($validation['warnings']),
            ]
        ]);
    }

    /**
     * Assign a professor to review the student's schedule.
     */
    private function assignProfessor(Student $student): Professor
    {
        // Get professors from the same school as the student
        $professors = Professor::where('school_id', $student->school_id)
            ->where('status', 'active')
            ->get();

        if ($professors->isEmpty()) {
            // Fallback to any active professor if no school-specific professors
            $professors = Professor::where('status', 'active')->get();
        }

        if ($professors->isEmpty()) {
            throw new \Exception('No active professors available for assignment.');
        }

        // Simple round-robin assignment based on current workload
        $professorWorkloads = [];
        
        foreach ($professors as $professor) {
            $workload = Enrollment::where('professor_id', $professor->id)
                ->whereIn('status', ['submitted', 'under_review'])
                ->count();
            
            $professorWorkloads[$professor->id] = $workload;
        }

        // Assign to professor with lowest workload
        $assignedProfessorId = array_search(min($professorWorkloads), $professorWorkloads);
        
        return Professor::find($assignedProfessorId);
    }

    /**
     * Log submission activity for audit trail.
     */
    private function logSubmissionActivity(Enrollment $enrollment, array $validation): void
    {
        // In a real system, this would log to an audit table
        \Log::info('Schedule submitted for review', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'professor_id' => $enrollment->professor_id,
            'total_units' => $validation['unit_load'],
            'course_count' => $enrollment->courses->count(),
            'submitted_at' => now(),
        ]);
    }

    /**
     * Get submission status for a student.
     */
    public function getSubmissionStatus(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollment = $student->getCurrentEnrollment();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No enrollment found.'
            ], 404);
        }

        $response = [
            'success' => true,
            'enrollment' => [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'total_units' => $enrollment->total_units,
                'submitted_at' => $enrollment->submitted_at,
                'created_at' => $enrollment->created_at,
                'updated_at' => $enrollment->updated_at,
            ],
            'courses' => $enrollment->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'units' => $course->units,
                ];
            }),
        ];

        // Add professor info if assigned
        if ($enrollment->professor_id) {
            $response['professor'] = [
                'name' => $enrollment->professor->full_name,
                'department' => $enrollment->professor->school->name,
                'email' => $enrollment->professor->email,
            ];
        }

        // Add validation data if available
        if ($enrollment->validation_data) {
            $response['validation'] = json_decode($enrollment->validation_data, true);
        }

        return response()->json($response);
    }

    /**
     * Prevent submission of invalid schedules.
     */
    public function checkSubmissionEligibility(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollment = $student->getCurrentEnrollment();

        if (!$enrollment) {
            return response()->json([
                'eligible' => false,
                'reason' => 'No enrollment found.'
            ]);
        }

        if ($enrollment->status !== 'draft') {
            return response()->json([
                'eligible' => false,
                'reason' => 'Enrollment has already been submitted or is not in draft status.'
            ]);
        }

        $courses = $enrollment->courses;
        
        if ($courses->isEmpty()) {
            return response()->json([
                'eligible' => false,
                'reason' => 'No courses selected for enrollment.'
            ]);
        }

        $validation = $this->validationService->validateSchedule($student, $courses);

        return response()->json([
            'eligible' => $validation['is_valid'],
            'reason' => $validation['is_valid'] ? 'Schedule is valid for submission.' : 'Schedule has validation errors.',
            'validation_summary' => [
                'total_units' => $validation['unit_load'],
                'error_count' => count($validation['errors']),
                'warning_count' => count($validation['warnings']),
                'errors' => $validation['errors'],
            ]
        ]);
    }
}