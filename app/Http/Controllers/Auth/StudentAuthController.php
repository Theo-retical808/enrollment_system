<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentAuthController extends Controller
{
    /**
     * Show the student login form.
     */
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    /**
     * Handle student login attempt.
     */
    public function login(Request $request)
    {
        // Log the login attempt
        \Log::info('Student login attempt', [
            'student_id' => $request->student_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('student_id', $request->student_id)->first();

        if (!$student) {
            \Log::warning('Student not found', ['student_id' => $request->student_id]);
            throw ValidationException::withMessages([
                'student_id' => ['Invalid student ID or password. Student ID format: 2024-001 (with 3 digits after dash)'],
            ]);
        }

        // Check account status
        if ($student->status !== 'active') {
            \Log::warning('Inactive student account', [
                'student_id' => $request->student_id,
                'status' => $student->status
            ]);
            throw ValidationException::withMessages([
                'student_id' => ['Your account is ' . $student->status . '. Please contact the registrar.'],
            ]);
        }

        if (!Hash::check($request->password, $student->password)) {
            \Log::warning('Invalid password', ['student_id' => $request->student_id]);
            throw ValidationException::withMessages([
                'student_id' => ['Invalid student ID or password.'],
            ]);
        }

        // Login successful
        \Log::info('Student login successful', [
            'student_id' => $student->student_id,
            'student_name' => $student->full_name,
        ]);

        Auth::guard('student')->login($student, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'));
    }

    /**
     * Handle student logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}