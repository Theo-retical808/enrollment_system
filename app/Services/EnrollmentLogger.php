<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\Log;

class EnrollmentLogger
{
    /**
     * Log student authentication events.
     */
    public static function logAuthentication(Student $student, string $action, bool $success, array $context = []): void
    {
        $level = $success ? 'info' : 'warning';
        
        Log::channel('enrollment')->{$level}("Student authentication: {$action}", [
            'student_id' => $student->id,
            'student_number' => $student->student_id,
            'action' => $action,
            'success' => $success,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
            ...$context,
        ]);
    }

    /**
     * Log enrollment workflow events.
     */
    public static function logEnrollmentAction(Enrollment $enrollment, string $action, array $context = []): void
    {
        Log::channel('enrollment')->info("Enrollment action: {$action}", [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'student_number' => $enrollment->student->student_id,
            'status' => $enrollment->status,
            'action' => $action,
            'total_units' => $enrollment->total_units,
            'course_count' => $enrollment->courses()->count(),
            'timestamp' => now(),
            ...$context,
        ]);
    }

    /**
     * Log course selection events.
     */
    public static function logCourseSelection(Student $student, Course $course, string $action, bool $success, array $context = []): void
    {
        $level = $success ? 'info' : 'warning';
        
        Log::channel('enrollment')->{$level}("Course selection: {$action}", [
            'student_id' => $student->id,
            'student_number' => $student->student_id,
            'course_id' => $course->id,
            'course_code' => $course->course_code,
            'course_title' => $course->title,
            'action' => $action,
            'success' => $success,
            'timestamp' => now(),
            ...$context,
        ]);
    }

    /**
     * Log validation events.
     */
    public static function logValidation(Student $student, string $validationType, bool $passed, array $details = []): void
    {
        $level = $passed ? 'info' : 'warning';
        
        Log::channel('enrollment')->{$level}("Validation: {$validationType}", [
            'student_id' => $student->id,
            'student_number' => $student->student_id,
            'validation_type' => $validationType,
            'passed' => $passed,
            'details' => $details,
            'timestamp' => now(),
        ]);
    }

    /**
     * Log professor review events.
     */
    public static function logProfessorReview(Enrollment $enrollment, string $action, string $decision = null, array $context = []): void
    {
        Log::channel('enrollment')->info("Professor review: {$action}", [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'professor_id' => $enrollment->professor_id,
            'action' => $action,
            'decision' => $decision,
            'old_status' => $enrollment->getOriginal('status'),
            'new_status' => $enrollment->status,
            'timestamp' => now(),
            ...$context,
        ]);
    }

    /**
     * Log payment verification events.
     */
    public static function logPaymentVerification(Student $student, bool $verified, array $context = []): void
    {
        $level = $verified ? 'info' : 'warning';
        
        Log::channel('enrollment')->{$level}('Payment verification', [
            'student_id' => $student->id,
            'student_number' => $student->student_id,
            'verified' => $verified,
            'timestamp' => now(),
            ...$context,
        ]);
    }

    /**
     * Log system errors.
     */
    public static function logError(string $context, \Throwable $exception, array $additionalData = []): void
    {
        Log::channel('enrollment')->error("System error: {$context}", [
            'context' => $context,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now(),
            ...$additionalData,
        ]);
    }

    /**
     * Log performance metrics.
     */
    public static function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $level = $duration > 1.0 ? 'warning' : 'info';
        
        Log::channel('performance')->{$level}("Performance: {$operation}", [
            'operation' => $operation,
            'duration_seconds' => round($duration, 3),
            'timestamp' => now(),
            ...$context,
        ]);
    }
}
