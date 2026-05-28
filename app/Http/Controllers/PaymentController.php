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
     * Check payment status.
     */
    public function checkStatus()
    {
        $student = Auth::guard('student')->user();
        $paymentStatus = $this->paymentService->getPaymentStatus($student);
        
        return response()->json($paymentStatus);
    }
}