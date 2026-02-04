<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';

// Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing Enrollment System...\n\n";

// Test 1: Check if application boots
try {
    echo "✓ Laravel application boots successfully\n";
} catch (Exception $e) {
    echo "✗ Laravel application failed to boot: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check if routes are defined
try {
    $routes = app('router')->getRoutes();
    $routeNames = [];
    foreach ($routes as $route) {
        if ($route->getName()) {
            $routeNames[] = $route->getName();
        }
    }
    
    $requiredRoutes = [
        'student.login',
        'student.dashboard',
        'student.enrollment.irregular',
        'student.enrollment.irregular.add-course',
        'student.enrollment.irregular.course-schedules',
        'student.enrollment.validation.feedback',
        'student.enrollment.validation.course'
    ];
    
    $missingRoutes = [];
    foreach ($requiredRoutes as $route) {
        if (!in_array($route, $routeNames)) {
            $missingRoutes[] = $route;
        }
    }
    
    if (empty($missingRoutes)) {
        echo "✓ All required routes are defined\n";
    } else {
        echo "✗ Missing routes: " . implode(', ', $missingRoutes) . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Route check failed: " . $e->getMessage() . "\n";
}

// Test 3: Check if controllers exist
$controllers = [
    'App\Http\Controllers\IrregularEnrollmentController',
    'App\Services\IrregularStudentEnrollmentService',
    'App\Services\PaymentVerificationService',
    'App\Services\ScheduleValidationService'
];

foreach ($controllers as $controller) {
    if (class_exists($controller)) {
        echo "✓ $controller exists\n";
    } else {
        echo "✗ $controller does not exist\n";
    }
}

// Test 4: Check if models exist
$models = [
    'App\Models\Student',
    'App\Models\Course',
    'App\Models\Enrollment',
    'App\Models\School'
];

foreach ($models as $model) {
    if (class_exists($model)) {
        echo "✓ $model exists\n";
    } else {
        echo "✗ $model does not exist\n";
    }
}

echo "\nTest completed!\n";