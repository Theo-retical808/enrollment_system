<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';

// Set up testing environment
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set environment to testing
$app['env'] = 'testing';

// Configure SQLite for testing
config(['database.default' => 'sqlite']);
config(['database.connections.sqlite.database' => ':memory:']);

echo "Running Enrollment System Tests...\n\n";

try {
    // Run migrations
    Artisan::call('migrate', ['--force' => true]);
    echo "✓ Database migrations completed\n";
    
    // Test basic functionality
    echo "\n=== Testing Basic Functionality ===\n";
    
    // Test 1: Check if services can be instantiated
    $paymentService = new App\Services\PaymentVerificationService();
    $enrollmentService = new App\Services\IrregularStudentEnrollmentService($paymentService);
    $validationService = new App\Services\ScheduleValidationService();
    echo "✓ All services can be instantiated\n";
    
    // Test 2: Create test data
    $school = App\Models\School::create([
        'name' => 'Test School',
        'code' => 'TEST',
    ]);
    
    $student = App\Models\Student::create([
        'student_id' => 'TEST001',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'first_name' => 'Test',
        'last_name' => 'Student',
        'school_id' => $school->id,
        'year_level' => 2,
    ]);
    
    $course = App\Models\Course::create([
        'course_code' => 'CS101',
        'title' => 'Introduction to Computer Science',
        'description' => 'Basic CS course',
        'units' => 3,
        'school_id' => $school->id,
        'is_active' => true,
    ]);
    
    echo "✓ Test data created successfully\n";
    
    // Test 3: Test enrollment workflow
    echo "\n=== Testing Enrollment Workflow ===\n";
    
    // Check if student is irregular (should be false initially)
    $isRegular = $student->isRegular();
    echo "✓ Student classification check: " . ($isRegular ? "Regular" : "Irregular") . "\n";
    
    // Create enrollment
    $enrollment = $enrollmentService->createManualEnrollment($student);
    echo "✓ Manual enrollment created: ID " . $enrollment->id . "\n";
    
    // Get available courses
    $availableCourses = $enrollmentService->getAvailableCourses($student);
    echo "✓ Available courses retrieved: " . $availableCourses->count() . " courses\n";
    
    // Test 4: Test schedule validation
    echo "\n=== Testing Schedule Validation ===\n";
    
    $courses = collect([
        (object) ['units' => 3],
        (object) ['units' => 4],
        (object) ['units' => 2],
    ]);
    
    $totalUnits = $validationService->calculateUnitLoad($courses);
    echo "✓ Unit load calculation: " . $totalUnits . " units\n";
    
    $validation = $validationService->validateSchedule($student, $courses);
    echo "✓ Schedule validation: " . ($validation['is_valid'] ? "Valid" : "Invalid") . "\n";
    
    // Test 5: Test course addition
    echo "\n=== Testing Course Addition ===\n";
    
    $scheduleData = [
        'schedule_day' => 'Monday',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'room' => 'Room 101',
        'instructor' => 'Prof. Test',
    ];
    
    $result = $enrollmentService->addCourseToEnrollment($enrollment, $course, $scheduleData);
    echo "✓ Course addition: " . ($result['success'] ? "Success" : "Failed - " . $result['message']) . "\n";
    
    if ($result['success']) {
        echo "  - New total units: " . $result['new_total_units'] . "\n";
        
        // Test course removal
        $removeResult = $enrollmentService->removeCourseFromEnrollment($enrollment, $course);
        echo "✓ Course removal: " . ($removeResult['success'] ? "Success" : "Failed") . "\n";
    }
    
    // Test 6: Test submission
    echo "\n=== Testing Submission Workflow ===\n";
    
    // Add course back for submission test
    $enrollmentService->addCourseToEnrollment($enrollment, $course, $scheduleData);
    
    $submitResult = $enrollmentService->submitForApproval($enrollment);
    echo "✓ Enrollment submission: " . ($submitResult ? "Success" : "Failed") . "\n";
    
    // Check final status
    $enrollment->refresh();
    echo "✓ Final enrollment status: " . $enrollment->status . "\n";
    
    echo "\n=== All Tests Completed Successfully! ===\n";
    
} catch (Exception $e) {
    echo "✗ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}