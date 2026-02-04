<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RegularStudentEnrollmentService;
use App\Services\PaymentVerificationService;
use App\Services\ScheduleValidationService;

class RegularEnrollmentController extends Controller
{
    protected $enrollmentService;
    protected $paymentService;
    protected $validationService;

    public function __construct(
        RegularStudentEnrollmentService $enrollmentService,
        PaymentVerificationService $paymentService,
        ScheduleValidationService $validationService
    ) {
        $this->enrollmentService = $enrollmentService;
        $this->paymentService = $paymentService;
        $this->validationService = $validationService;
    }

    /**
     * Show the regular student enrollment page.
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        // Check if student is regular
        if (!$student->isRegular()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This enrollment method is only available for regular students.');
        }

        // Get or create enrollment
        $enrollment = $this->enrollmentService->getStudentEnrollment($student);
        
        if (!$enrollment) {
            $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);
        }

        return view('student.regular-enrollment', compact('student', 'enrollment'));
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

        return redirect()->route('enrollment.regular')
            ->with('success', 'Enrollment has been reset. A new schedule will be assigned.');
    }
}