<?php

namespace App\Exceptions;

class PaymentRequiredException extends EnrollmentException
{
    public function __construct(string $semester, string $academicYear, float $amount = 0.0)
    {
        $message = "Enrollment fee payment required for {$semester} {$academicYear}.";
        
        parent::__construct(
            $message,
            'PAYMENT_REQUIRED',
            [
                'semester' => $semester,
                'academic_year' => $academicYear,
                'amount' => $amount,
            ],
            402
        );
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error_code' => $this->errorCode,
                'message' => $this->getMessage(),
                'context' => $this->context,
                'payment_url' => route('student.payment'),
            ], $this->getCode());
        }

        return redirect()->route('student.payment')
            ->with('error', $this->getMessage())
            ->with('payment_context', $this->context);
    }
}
