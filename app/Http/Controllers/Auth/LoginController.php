<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Professor;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the unified login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle unified login attempt.
     * Auto-detects role based on credentials format:
     * - Admin: admin_id (e.g., ADMIN001)
     * - Professor: professor_id (e.g., PROF001)
     * - Student: student_id (e.g., 2024-001)
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $userId = trim($request->user_id);
        $password = $request->password;

        // Try to detect and authenticate based on credential format
        // 1. Check if it's an admin
        $admin = Admin::where('admin_id', $userId)->first();
        if ($admin) {
            return $this->authenticateAdmin($request, $admin, $password);
        }

        // 2. Check if it's a professor
        $professor = Professor::where('professor_id', $userId)->first();
        if ($professor) {
            return $this->authenticateProfessor($request, $professor, $password);
        }

        // 3. Check if it's a student
        $student = Student::where('student_id', $userId)->first();
        if ($student) {
            return $this->authenticateStudent($request, $student, $password);
        }

        // No matching account found
        throw ValidationException::withMessages([
            'user_id' => ['Invalid credentials. No account found with this ID.'],
        ]);
    }

    /**
     * Authenticate an admin user.
     */
    private function authenticateAdmin(Request $request, Admin $admin, string $password)
    {
        if ($admin->status !== 'active') {
            throw ValidationException::withMessages([
                'user_id' => ['Your account is ' . $admin->status . '. Please contact the system administrator.'],
            ]);
        }

        if (!Hash::check($password, $admin->password)) {
            throw ValidationException::withMessages([
                'user_id' => ['Invalid credentials. Please check your password.'],
            ]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Authenticate a professor user.
     */
    private function authenticateProfessor(Request $request, Professor $professor, string $password)
    {
        if ($professor->status !== 'active') {
            throw ValidationException::withMessages([
                'user_id' => ['Your account is ' . $professor->status . '. Please contact administration.'],
            ]);
        }

        if (!Hash::check($password, $professor->password)) {
            throw ValidationException::withMessages([
                'user_id' => ['Invalid credentials. Please check your password.'],
            ]);
        }

        Auth::guard('professor')->login($professor, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('professor.dashboard'));
    }

    /**
     * Authenticate a student user.
     */
    private function authenticateStudent(Request $request, Student $student, string $password)
    {
        if ($student->status !== 'active') {
            throw ValidationException::withMessages([
                'user_id' => ['Your account is ' . $student->status . '. Please contact the registrar.'],
            ]);
        }

        if (!Hash::check($password, $student->password)) {
            throw ValidationException::withMessages([
                'user_id' => ['Invalid credentials. Please check your password.'],
            ]);
        }

        Auth::guard('student')->login($student, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'));
    }

    /**
     * Handle logout for any authenticated user.
     */
    public function logout(Request $request)
    {
        // Logout from all guards
        Auth::guard('admin')->logout();
        Auth::guard('professor')->logout();
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
