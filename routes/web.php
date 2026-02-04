<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\ProfessorAuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegularEnrollmentController;
use App\Http\Controllers\IrregularEnrollmentController;
use App\Http\Controllers\ScheduleSubmissionController;

Route::get('/', function () {
    return view('welcome');
});

// Fallback login route for Laravel's default auth redirects
Route::get('/login', function () {
    // Redirect to student login by default, or you could show a choice page
    return redirect()->route('student.login');
})->name('login');

// Test route to check authentication
Route::get('/test-auth', function () {
    return [
        'message' => 'Authentication system is working!',
        'student_guard' => Auth::guard('student')->check() ? 'Authenticated' : 'Not authenticated',
        'professor_guard' => Auth::guard('professor')->check() ? 'Authenticated' : 'Not authenticated',
    ];
});

// Debug route for irregular enrollment
Route::get('/debug-irregular', function () {
    $student = Auth::guard('student')->user();
    if (!$student) {
        return ['error' => 'Not authenticated'];
    }
    
    $enrollmentService = new \App\Services\IrregularStudentEnrollmentService(
        new \App\Services\PaymentVerificationService()
    );
    
    $availableCourses = $enrollmentService->getAvailableCourses($student);
    $enrollment = $enrollmentService->getStudentEnrollment($student);
    
    return [
        'student' => [
            'id' => $student->id,
            'name' => $student->full_name,
            'is_regular' => $student->isRegular(),
            'has_failed_courses' => $student->hasFailedCourses(),
            'school' => $student->school->name ?? 'No school',
        ],
        'enrollment' => $enrollment ? [
            'id' => $enrollment->id,
            'status' => $enrollment->status,
            'total_units' => $enrollment->total_units,
            'courses_count' => $enrollment->courses()->count(),
        ] : null,
        'available_courses_count' => $availableCourses->count(),
        'available_courses' => $availableCourses->take(5)->map(function($course) {
            return [
                'id' => $course->id,
                'code' => $course->course_code,
                'title' => $course->title,
                'units' => $course->units,
                'is_active' => $course->is_active,
            ];
        }),
        'failed_courses' => $student->completedCourses()
            ->wherePivot('passed', false)
            ->get()
            ->map(function($course) {
                return [
                    'code' => $course->course_code,
                    'title' => $course->title,
                    'grade' => $course->pivot->grade,
                ];
            }),
    ];
})->middleware('student.auth');

Route::get('/info', function () {
    return [
        'app_name' => config('app.name'),
        'environment' => config('app.env'),
        'database' => config('database.default'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'status' => 'Enrollment System is ready!',
        'routes' => [
            'student_login' => route('student.login'),
            'professor_login' => route('professor.login'),
        ]
    ];
});

// Student Authentication Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [StudentAuthController::class, 'login']);
    Route::post('logout', [StudentAuthController::class, 'logout'])->name('logout');
    
    // Protected student routes
    Route::middleware('student.auth')->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('schedule', [StudentDashboardController::class, 'viewSchedule'])->name('schedule');
        
        // Payment routes
        Route::get('payment/required', [PaymentController::class, 'paymentRequired'])->name('payment.required');
        Route::post('payment/simulate', [PaymentController::class, 'simulatePayment'])->name('payment.simulate');
        Route::get('payment/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
        
        // Enrollment routes (require payment)
        Route::middleware('payment.required')->group(function () {
            // Regular student enrollment
            Route::get('enrollment/regular', [RegularEnrollmentController::class, 'index'])->name('enrollment.regular');
            Route::post('enrollment/regular/submit', [RegularEnrollmentController::class, 'submit'])->name('enrollment.regular.submit');
            Route::post('enrollment/regular/reset', [RegularEnrollmentController::class, 'reset'])->name('enrollment.regular.reset');
            
            // Irregular student enrollment
            Route::get('enrollment/irregular', [IrregularEnrollmentController::class, 'index'])->name('enrollment.irregular');
            Route::post('enrollment/irregular/add-course', [IrregularEnrollmentController::class, 'addCourse'])->name('enrollment.irregular.add-course');
            Route::post('enrollment/irregular/remove-course', [IrregularEnrollmentController::class, 'removeCourse'])->name('enrollment.irregular.remove-course');
            Route::get('enrollment/irregular/course-schedules/{course}', [IrregularEnrollmentController::class, 'getCourseSchedules'])->name('enrollment.irregular.course-schedules');
            Route::post('enrollment/irregular/submit', [IrregularEnrollmentController::class, 'submit'])->name('enrollment.irregular.submit');
            Route::post('enrollment/irregular/petition', [IrregularEnrollmentController::class, 'submitPetition'])->name('enrollment.irregular.petition');
            Route::post('enrollment/irregular/reset', [IrregularEnrollmentController::class, 'reset'])->name('enrollment.irregular.reset');
            
            // Real-time validation endpoints
            Route::get('enrollment/validation/feedback', [IrregularEnrollmentController::class, 'getValidationFeedback'])->name('enrollment.validation.feedback');
            Route::post('enrollment/validation/course', [IrregularEnrollmentController::class, 'validateCourseAddition'])->name('enrollment.validation.course');
            Route::post('enrollment/validation/conflict-resolution', [IrregularEnrollmentController::class, 'getConflictResolution'])->name('enrollment.validation.conflict-resolution');
            
            // Schedule submission endpoints
            Route::post('enrollment/submit', [ScheduleSubmissionController::class, 'submitSchedule'])->name('enrollment.submit');
            Route::get('enrollment/validate', [ScheduleSubmissionController::class, 'validateBeforeSubmission'])->name('enrollment.validate');
            Route::get('enrollment/status', [ScheduleSubmissionController::class, 'getSubmissionStatus'])->name('enrollment.status');
            Route::get('enrollment/eligibility', [ScheduleSubmissionController::class, 'checkSubmissionEligibility'])->name('enrollment.eligibility');
        });
    });
});

// Professor Authentication Routes
Route::prefix('professor')->name('professor.')->group(function () {
    Route::get('login', [ProfessorAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [ProfessorAuthController::class, 'login']);
    Route::post('logout', [ProfessorAuthController::class, 'logout'])->name('logout');
    
    // Protected professor routes
    Route::middleware('professor.auth')->group(function () {
        Route::get('dashboard', function () {
            return view('professor.dashboard');
        })->name('dashboard');
    });
});
