<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentVerificationService;

class CheckPaymentStatus
{
    protected $paymentService;

    public function __construct(PaymentVerificationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check payment status for authenticated students
        if (!Auth::guard('student')->check()) {
            return $next($request);
        }

        $student = Auth::guard('student')->user();
        $accessInfo = $this->paymentService->canAccessEnrollment($student);

        // If payment is not verified, redirect to payment page
        if (!$accessInfo['can_access']) {
            return redirect()->route('student.payment.required')
                ->with('payment_status', $accessInfo['payment_status'])
                ->with('message', $accessInfo['reason']);
        }

        return $next($request);
    }
}