<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

// Public Course and Finance Routes
Route::get('/courses', [CourseManagementController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [CourseManagementController::class, 'show'])->name('courses.show');
Route::get('/finances', [FinanceController::class, 'index'])->name('finance.index');

// Fallback login route for Laravel's default auth redirects
Route::get('/login', function () {
    return redirect()->route('student.login');
})->name('login');

// Student Authentication Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [StudentAuthController::class, 'login'])->middleware('rate.limit.login');
    Route::post('logout', [StudentAuthController::class, 'logout'])->name('logout');
    
    // Protected student routes
    Route::middleware('student.auth')->group(function () {
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
});

// Professor Authentication Routes
Route::prefix('professor')->name('professor.')->group(function () {
    Route::get('login', [ProfessorAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [ProfessorAuthController::class, 'login'])->middleware('rate.limit.login');
    Route::post('logout', [ProfessorAuthController::class, 'logout'])->name('logout');
    
    // Protected professor routes
    Route::middleware('professor.auth')->group(function () {
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
});

