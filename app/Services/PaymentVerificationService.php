<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Payment;

class PaymentVerificationService
{
    /**
     * Check if enrollment fee is paid for current semester.
     */
    public function isEnrollmentFeePaid(Student $student, string $semester = null, string $academicYear = null): bool
    {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        
        return $student->payments()
            ->where('payment_type', 'enrollment_fee')
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->where('status', 'paid')
            ->exists();
    }

    /**
     * Get payment status for a student.
     */
    public function getPaymentStatus(Student $student, string $semester = null, string $academicYear = null): array
    {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        
        $enrollmentFeePayment = $student->payments()
            ->where('payment_type', 'enrollment_fee')
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->first();
        
        if (!$enrollmentFeePayment) {
            return [
                'status' => 'not_found',
                'message' => 'No enrollment fee payment record found for this semester.',
                'can_enroll' => false,
                'payment_required' => true,
                'amount_due' => $this->getEnrollmentFeeAmount(),
            ];
        }
        
        switch ($enrollmentFeePayment->status) {
            case 'paid':
                return [
                    'status' => 'paid',
                    'message' => 'Enrollment fee has been paid successfully.',
                    'can_enroll' => true,
                    'payment_required' => false,
                    'paid_at' => $enrollmentFeePayment->paid_at,
                    'amount_paid' => $enrollmentFeePayment->amount,
                ];
                
            case 'pending':
                return [
                    'status' => 'pending',
                    'message' => 'Enrollment fee payment is pending verification.',
                    'can_enroll' => false,
                    'payment_required' => true,
                    'amount_due' => $enrollmentFeePayment->amount,
                ];
                
            case 'failed':
                return [
                    'status' => 'failed',
                    'message' => 'Enrollment fee payment failed. Please try again.',
                    'can_enroll' => false,
                    'payment_required' => true,
                    'amount_due' => $enrollmentFeePayment->amount,
                ];
                
            default:
                return [
                    'status' => 'unknown',
                    'message' => 'Payment status is unknown. Please contact the registrar.',
                    'can_enroll' => false,
                    'payment_required' => true,
                ];
        }
    }

    /**
     * Create enrollment fee payment record.
     */
    public function createEnrollmentFeePayment(Student $student, string $semester = null, string $academicYear = null): Payment
    {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        
        return Payment::create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'amount' => $this->getEnrollmentFeeAmount(),
            'status' => 'pending',
            'semester' => $semester,
            'academic_year' => $academicYear,
        ]);
    }

    /**
     * Mark enrollment fee as paid.
     */
    public function markEnrollmentFeePaid(Student $student, string $semester = null, string $academicYear = null): bool
    {
        $semester = $semester ?? $this->getCurrentSemester();
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        
        $payment = $student->payments()
            ->where('payment_type', 'enrollment_fee')
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->first();
        
        if ($payment) {
            $payment->markAsPaid();
            return true;
        }
        
        return false;
    }

    /**
     * Get enrollment fee amount.
     */
    public function getEnrollmentFeeAmount(): float
    {
        // This would typically come from a configuration or database
        return 5000.00; // Default enrollment fee
    }

    /**
     * Get current semester.
     */
    public function getCurrentSemester(): string
    {
        $month = date('n');
        
        if ($month >= 6 && $month <= 10) {
            return '1st Semester';
        } elseif ($month >= 11 || $month <= 3) {
            return '2nd Semester';
        } else {
            return 'Summer';
        }
    }

    /**
     * Get current academic year.
     */
    public function getCurrentAcademicYear(): string
    {
        $year = date('Y');
        $month = date('n');
        
        if ($month >= 6) {
            return $year . '-' . ($year + 1);
        } else {
            return ($year - 1) . '-' . $year;
        }
    }

    /**
     * Check if student can access enrollment features.
     */
    public function canAccessEnrollment(Student $student): array
    {
        $paymentStatus = $this->getPaymentStatus($student);
        
        return [
            'can_access' => $paymentStatus['can_enroll'],
            'reason' => $paymentStatus['can_enroll'] 
                ? 'Payment verified successfully' 
                : $paymentStatus['message'],
            'payment_status' => $paymentStatus,
        ];
    }

    /**
     * Get payment portal URL or information.
     */
    public function getPaymentPortalInfo(): array
    {
        return [
            'portal_url' => '#', // This would be the actual payment portal URL
            'instructions' => 'Please visit the cashier\'s office or use the online payment portal to pay your enrollment fee.',
            'office_hours' => 'Monday to Friday, 8:00 AM - 5:00 PM',
            'contact_number' => '(02) 123-4567',
        ];
    }
}