<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StudentClassificationService;
use App\Services\PaymentVerificationService;

class StudentDashboardController extends Controller
{
    protected $classificationService;
    protected $paymentService;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        StudentClassificationService $classificationService,
        PaymentVerificationService $paymentService
    ) {
        $this->classificationService = $classificationService;
        $this->paymentService = $paymentService;
    }

    /**
     * Show the student dashboard.
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        // Get student classification and routing information
        $classificationInfo = $this->classificationService->routeToEnrollment($student);
        $recommendedAction = $this->classificationService->getRecommendedAction($student);
        
        // Get payment status
        $paymentStatus = $this->paymentService->getPaymentStatus($student);
        $canAccessEnrollment = $this->paymentService->canAccessEnrollment($student);
        
        return view('student.dashboard', compact(
            'student', 
            'currentEnrollment', 
            'classificationInfo', 
            'recommendedAction',
            'paymentStatus',
            'canAccessEnrollment'
        ));
    }

    /**
     * Show the student's current schedule.
     */
    public function viewSchedule()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        if (!$currentEnrollment) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No enrollment found.');
        }
        
        return view('student.schedule', compact('student', 'currentEnrollment'));
    }
}