<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentVerificationService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentVerificationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show payment required page.
     */
    public function paymentRequired()
    {
        $student = Auth::guard('student')->user();
        $paymentStatus = $this->paymentService->getPaymentStatus($student);
        $paymentPortalInfo = $this->paymentService->getPaymentPortalInfo();
        
        return view('student.payment-required', compact('student', 'paymentStatus', 'paymentPortalInfo'));
    }

    /**
     * Simulate payment (for demo purposes).
     */
    public function simulatePayment(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Create payment record if it doesn't exist
        $existingPayment = $student->payments()
            ->where('payment_type', 'enrollment_fee')
            ->where('semester', $this->paymentService->getCurrentSemester())
            ->where('academic_year', $this->paymentService->getCurrentAcademicYear())
            ->first();
        
        if (!$existingPayment) {
            $this->paymentService->createEnrollmentFeePayment($student);
        }
        
        // Mark as paid
        $this->paymentService->markEnrollmentFeePaid($student);
        
        return redirect()->route('student.dashboard')
            ->with('success', 'Payment has been processed successfully! You can now proceed with enrollment.');
    }

    /**
     * Check payment status.
     */
    public function checkStatus()
    {
        $student = Auth::guard('student')->user();
        $paymentStatus = $this->paymentService->getPaymentStatus($student);
        
        return response()->json($paymentStatus);
    }
}