<?php

use Illuminate\Support\Facades\Route;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

// Test route to verify login credentials
Route::get('/test-login-debug', function () {
    $student = Student::where('student_id', '2024-001')->first();
    
    if (!$student) {
        return response()->json([
            'error' => 'Student not found',
            'student_id' => '2024-001'
        ]);
    }
    
    $testPassword = 'password';
    $passwordCheck = Hash::check($testPassword, $student->password);
    
    return response()->json([
        'student_found' => true,
        'student_id' => $student->student_id,
        'email' => $student->email,
        'status' => $student->status,
        'password_hash_exists' => !empty($student->password),
        'password_check_result' => $passwordCheck,
        'test_password' => $testPassword,
        'message' => $passwordCheck ? 'Password is correct!' : 'Password does NOT match!',
    ]);
});
