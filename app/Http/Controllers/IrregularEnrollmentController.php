<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\IrregularStudentEnrollmentService;
use App\Services\PaymentVerificationService;
use App\Services\ScheduleValidationService;
use App\Models\Course;

class IrregularEnrollmentController extends Controller
{
    protected $enrollmentService;
    protected $paymentService;
    protected $validationService;

    public function __construct(
        IrregularStudentEnrollmentService $enrollmentService,
        PaymentVerificationService $paymentService,
        ScheduleValidationService $validationService
    ) {
        $this->enrollmentService = $enrollmentService;
        $this->paymentService = $paymentService;
        $this->validationService = $validationService;
    }

    /**
     * Show the irregular student enrollment page.
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Check if student is irregular
        if ($student->isRegular()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This enrollment method is only available for irregular students.');
        }

        // Get or create enrollment
        $enrollment = $this->enrollmentService->getStudentEnrollment($student);
        
        if (!$enrollment) {
            $enrollment = $this->enrollmentService->createManualEnrollment($student);
        }

        // Get available courses
        $search = $request->get('search');
        $availableCourses = $this->enrollmentService->getAvailableCourses($student, $search);
        
        // Get failed courses for petitions
        $failedCourses = $this->enrollmentService->getFailedCourses($student);
        
        // Get student petitions
        $petitions = $this->enrollmentService->getStudentPetitions($student);

        return view('student.irregular-enrollment', compact(
            'student', 
            'enrollment', 
            'availableCourses', 
            'failedCourses', 
            'petitions',
            'search'
        ));
    }

    /**
     * Add course to enrollment (AJAX).
     */
    public function addCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'schedule_day' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room' => 'nullable|string',
            'instructor' => 'nullable|string',
        ]);

        $student = Auth::guard('student')->user();
        $enrollment = $this->enrollmentService->getStudentEnrollment($student);
        $course = Course::findOrFail($request->course_id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No enrollment found. Please refresh the page.',
            ]);
        }

        $result = $this->enrollmentService->addCourseToEnrollment($enrollment, $course, [
            'schedule_day' => $request->schedule_day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
            'instructor' => $request->instructor,
        ]);

        return response()->json($result);
    }

    /**
     * Remove course from enrollment (AJAX).
     */
    public function removeCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Auth::guard('student')->user();
        $enrollment = $this->enrollmentService->getStudentEnrollment($student);
        $course = Course::findOrFail($request->course_id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No enrollment found. Please refresh the page.',
            ]);
        }

        $result = $this->enrollmentService->removeCourseFromEnrollment($enrollment, $course);

        return response()->json($result);
    }

    /**
     * Get course schedule options (AJAX).
     */
    public function getCourseSchedules(Course $course)
    {
        $schedules = $this->enrollmentService->getCourseScheduleOptions($course);
        
        return response()->json([
            'success' => true,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Submit enrollment for approval.
     */
    public function submit(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollment = $this->enrollmentService->getStudentEnrollment($student);

        if (!$enrollment) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No enrollment found. Please try again.');
        }

        if ($enrollment->status !== 'draft') {
            return redirect()->route('student.dashboard')
                ->with('error', 'Enrollment has already been submitted.');
        }

        if ($enrollment->courses()->count() === 0) {
            return redirect()->back()
                ->with('error', 'Please add at least one course before submitting.');
        }

        $success = $this->enrollmentService->submitForApproval($enrollment);

        if ($success) {
            return redirect()->route('student.dashboard')
                ->with('success', 'Your enrollment has been submitted for approval!');
        } else {
            return redirect()->back()
                ->with('error', 'Failed to submit enrollment. Please try again.');
        }
    }

    /**
     * Submit petition for failed course.
     */
    public function submitPetition(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'justification' => 'required|string|min:10|max:1000',
        ]);

        $student = Auth::guard('student')->user();
        $course = Course::findOrFail($request->course_id);

        // Check if student has failed this course
        $hasFailedCourse = $student->completedCourses()
            ->where('courses.id', $course->id)
            ->wherePivot('passed', false)
            ->exists();

        if (!$hasFailedCourse) {
            return redirect()->back()
                ->with('error', 'You can only petition for courses you have previously failed.');
        }

        // Check if petition already exists
        $existingPetition = $student->petitions()
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPetition) {
            return redirect()->back()
                ->with('error', 'You already have a pending petition for this course.');
        }

        $this->enrollmentService->createPetition($student, $course, $request->justification);

        return redirect()->back()
            ->with('success', 'Petition submitted successfully! You will be notified of the decision.');
    }

    /**
     * Reset enrollment (for testing purposes).
     */
    public function reset()
    {
        $student = Auth::guard('student')->user();
        $enrollment = $this->enrollmentService->getStudentEnrollment($student);

        if ($enrollment) {
            $enrollment->courses()->detach();
            $enrollment->delete();
        }

        return redirect()->route('student.enrollment.irregular')
            ->with('success', 'Enrollment has been reset. You can now select new courses.');
    }

    /**
     * Get real-time validation feedback for current enrollment.
     */
    public function getValidationFeedback(Request $request)
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
            'summary' => [
                'total_courses' => $courses->count(),
                'total_units' => $validation['unit_load'],
                'remaining_units' => ScheduleValidationService::MAX_UNIT_LOAD - $validation['unit_load'],
                'can_add_more' => $validation['unit_load'] < ScheduleValidationService::MAX_UNIT_LOAD && $validation['is_valid'],
                'is_valid' => $validation['is_valid'],
                'unit_percentage' => min(($validation['unit_load'] / ScheduleValidationService::MAX_UNIT_LOAD) * 100, 100),
            ],
            'detailed_feedback' => [
                'prerequisite_violations' => $validation['prerequisite_violations'],
                'schedule_conflicts' => $validation['schedule_conflicts'],
                'unit_limit_exceeded' => $validation['unit_limit_exceeded'],
            ]
        ]);
    }

    /**
     * Validate adding a specific course to current enrollment.
     */
    public function validateCourseAddition(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'schedule_day' => 'nullable|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
        ]);

        $student = Auth::guard('student')->user();
        $enrollment = $student->getCurrentEnrollment();
        $course = Course::findOrFail($request->course_id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No enrollment found.'
            ], 404);
        }

        $existingCourses = $enrollment->courses;

        // If schedule data is provided, validate with specific schedule
        if ($request->has(['schedule_day', 'start_time', 'end_time'])) {
            $scheduleData = [
                'schedule_day' => $request->schedule_day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ];
            
            $canAdd = $this->validationService->canAddCourseWithSchedule($student, $course, $existingCourses, $scheduleData);
        } else {
            // General validation without specific schedule
            $canAdd = $this->validationService->canAddCourse($student, $course, $existingCourses);
        }

        return response()->json([
            'success' => true,
            'can_add' => $canAdd['can_add'],
            'reasons' => $canAdd['reasons'],
            'new_unit_load' => $canAdd['new_unit_load'],
            'would_exceed_limit' => $canAdd['would_exceed_limit'],
            'detailed_feedback' => [
                'prerequisite_violations' => $canAdd['prerequisite_violations'] ?? [],
                'schedule_conflicts' => $canAdd['schedule_conflicts'] ?? [],
            ]
        ]);
    }

    /**
     * Get courses that would become available after removing a specific course.
     */
    public function getConflictResolution(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Auth::guard('student')->user();
        $enrollment = $student->getCurrentEnrollment();
        $courseToRemove = Course::findOrFail($request->course_id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No enrollment found.'
            ], 404);
        }

        // Get current courses excluding the one to be removed
        $remainingCourses = $enrollment->courses->filter(function ($course) use ($courseToRemove) {
            return $course->id !== $courseToRemove->id;
        });

        // Get all available courses
        $availableCourses = $this->enrollmentService->getAvailableCourses($student);
        
        // Check which courses would become available
        $resolvedCourses = [];
        foreach ($availableCourses as $course) {
            if ($enrollment->courses->contains('id', $course->id)) {
                continue; // Skip already enrolled courses
            }

            $canAdd = $this->validationService->canAddCourse($student, $course, $remainingCourses);
            if ($canAdd['can_add']) {
                $resolvedCourses[] = [
                    'id' => $course->id,
                    'course_code' => $course->course_code,
                    'title' => $course->title,
                    'units' => $course->units,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'resolved_courses' => $resolvedCourses,
            'message' => count($resolvedCourses) > 0 
                ? 'Removing this course would make ' . count($resolvedCourses) . ' course(s) available.'
                : 'No additional courses would become available.'
        ]);
    }
}