<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\ProfessorAuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\ProfessorDashboardController;
use App\Http\Controllers\ProfessorReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegularEnrollmentController;
use App\Http\Controllers\IrregularEnrollmentController;
use App\Http\Controllers\ScheduleSubmissionController;
use App\Http\Controllers\AuditReportController;
use App\Http\Controllers\CourseManagementController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Course and Finance Routes
Route::get('/courses', [CourseManagementController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CourseManagementController::class, 'show'])->name('courses.show');
Route::get('/finances', [FinanceController::class, 'index'])->name('finance.index');

// Unified Login (single login page, auto-detects role)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('rate.limit.login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Legacy routes redirect to unified login
Route::get('/student/login', function () {
    return redirect()->route('login');
})->name('student.login');
Route::get('/professor/login', function () {
    return redirect()->route('login');
})->name('professor.login');

// Student legacy login/logout (keep POST for backward compatibility)
Route::post('/student/login', [LoginController::class, 'login'])->middleware('rate.limit.login');
Route::post('/student/logout', [LoginController::class, 'logout'])->name('student.logout');

// Professor legacy login/logout (keep POST for backward compatibility)
Route::post('/professor/login', [LoginController::class, 'login'])->middleware('rate.limit.login');
Route::post('/professor/logout', [LoginController::class, 'logout'])->name('professor.logout');

// Student Protected Routes
Route::prefix('student')->name('student.')->middleware('student.auth')->group(function () {
    Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('courses', [StudentDashboardController::class, 'viewCourses'])->name('courses');
    Route::get('schedule', [StudentDashboardController::class, 'viewSchedule'])->name('schedule');
    Route::get('finances', [FinanceController::class, 'studentFinances'])->name('finances');
    
    // Schedule export routes
    Route::get('schedule/export/pdf', [StudentDashboardController::class, 'exportPdf'])->name('schedule.export.pdf');
    Route::get('schedule/export/csv', [StudentDashboardController::class, 'exportCsv'])->name('schedule.export.csv');
    Route::post('schedule/email', [StudentDashboardController::class, 'emailSchedule'])->name('schedule.email');
    
    // Payment routes
    Route::get('payment/required', [PaymentController::class, 'paymentRequired'])->name('payment.required');
    Route::post('payment/simulate', [PaymentController::class, 'simulatePayment'])->name('payment.simulate');
    Route::get('payment/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
    
    // Enrollment routes (require payment)
    Route::middleware('payment.required')->group(function () {
        // Regular student enrollment (with rate limiting)
        Route::middleware('rate.limit.enrollment')->group(function () {
            Route::get('enrollment/regular', [RegularEnrollmentController::class, 'index'])->name('enrollment.regular');
            Route::post('enrollment/regular/submit', [RegularEnrollmentController::class, 'submit'])->name('enrollment.regular.submit');
            Route::post('enrollment/regular/reset', [RegularEnrollmentController::class, 'reset'])->name('enrollment.regular.reset');
        });
        
        // Irregular student enrollment (with rate limiting)
        Route::middleware('rate.limit.enrollment')->group(function () {
            Route::get('enrollment/irregular', [IrregularEnrollmentController::class, 'index'])->name('enrollment.irregular');
            Route::post('enrollment/irregular/add-course', [IrregularEnrollmentController::class, 'addCourse'])->name('enrollment.irregular.add-course');
            Route::post('enrollment/irregular/remove-course', [IrregularEnrollmentController::class, 'removeCourse'])->name('enrollment.irregular.remove-course');
            Route::get('enrollment/irregular/course-schedules/{course}', [IrregularEnrollmentController::class, 'getCourseSchedules'])->name('enrollment.irregular.course-schedules');
            Route::post('enrollment/irregular/submit', [IrregularEnrollmentController::class, 'submit'])->name('enrollment.irregular.submit');
            Route::post('enrollment/irregular/petition', [IrregularEnrollmentController::class, 'submitPetition'])->name('enrollment.irregular.petition');
            Route::post('enrollment/irregular/reset', [IrregularEnrollmentController::class, 'reset'])->name('enrollment.irregular.reset');
        });
        
        // Real-time validation endpoints
        Route::get('enrollment/validation/feedback', [IrregularEnrollmentController::class, 'getValidationFeedback'])->name('enrollment.validation.feedback');
        Route::post('enrollment/validation/course', [IrregularEnrollmentController::class, 'validateCourseAddition'])->name('enrollment.validation.course');
        Route::post('enrollment/validation/conflict-resolution', [IrregularEnrollmentController::class, 'getConflictResolution'])->name('enrollment.validation.conflict-resolution');
        
        // Schedule submission endpoints (with rate limiting)
        Route::middleware('rate.limit.enrollment')->group(function () {
            Route::post('enrollment/submit', [ScheduleSubmissionController::class, 'submitSchedule'])->name('enrollment.submit');
            Route::get('enrollment/validate', [ScheduleSubmissionController::class, 'validateBeforeSubmission'])->name('enrollment.validate');
            Route::get('enrollment/status', [ScheduleSubmissionController::class, 'getSubmissionStatus'])->name('enrollment.status');
            Route::get('enrollment/eligibility', [ScheduleSubmissionController::class, 'checkSubmissionEligibility'])->name('enrollment.eligibility');
        });
    });
});

// Professor Protected Routes
Route::prefix('professor')->name('professor.')->middleware('professor.auth')->group(function () {
    Route::get('dashboard', [ProfessorDashboardController::class, 'index'])->name('dashboard');
    Route::get('review/{enrollment}', [ProfessorDashboardController::class, 'reviewSchedule'])->name('review');
    Route::post('review/{enrollment}/process', [ProfessorReviewController::class, 'processReview'])->name('approve');
    Route::get('review/{enrollment}/status', [ProfessorReviewController::class, 'getReviewStatus'])->name('review.status');
    
    // Audit report routes (for administrators/professors)
    Route::get('audit/enrollment/{enrollment}', [AuditReportController::class, 'getEnrollmentAuditTrail'])->name('audit.enrollment');
    Route::get('audit/report', [AuditReportController::class, 'getAuditReport'])->name('audit.report');
    Route::get('audit/statistics', [AuditReportController::class, 'getAuditStatistics'])->name('audit.statistics');
    Route::get('audit/export', [AuditReportController::class, 'exportAuditReport'])->name('audit.export');
});

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('accounts', [AdminDashboardController::class, 'accounts'])->name('accounts');
    Route::get('payments', [AdminDashboardController::class, 'payments'])->name('payments');
    Route::post('payments/{payment}/confirm', [AdminDashboardController::class, 'confirmPayment'])->name('payments.confirm');
    Route::post('payments/{payment}/reject', [AdminDashboardController::class, 'rejectPayment'])->name('payments.reject');
    Route::get('enrollments', [AdminDashboardController::class, 'enrollments'])->name('enrollments');
    Route::post('enrollments/{enrollment}/override', [AdminDashboardController::class, 'overrideEnrollment'])->name('enrollments.override');
});
