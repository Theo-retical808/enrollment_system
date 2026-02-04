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
        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('student_id', $request->student_id)->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'student_id' => ['Invalid student ID or password.'],
            ]);
        }

        // Check account status
        if ($student->status !== 'active') {
            throw ValidationException::withMessages([
                'student_id' => ['Your account is ' . $student->status . '. Please contact the registrar.'],
            ]);
        }

        if (!Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'student_id' => ['Invalid student ID or password.'],
            ]);
        }

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