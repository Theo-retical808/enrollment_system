<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfessorAuthController extends Controller
{
    /**
     * Show the professor login form.
     */
    public function showLoginForm()
    {
        return view('auth.professor-login');
    }

    /**
     * Handle professor login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'professor_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $professor = Professor::where('professor_id', $request->professor_id)->first();

        if (!$professor) {
            throw ValidationException::withMessages([
                'professor_id' => ['Invalid professor ID or password.'],
            ]);
        }

        // Check account status
        if ($professor->status !== 'active') {
            throw ValidationException::withMessages([
                'professor_id' => ['Your account is ' . $professor->status . '. Please contact administration.'],
            ]);
        }

        if (!Hash::check($request->password, $professor->password)) {
            throw ValidationException::withMessages([
                'professor_id' => ['Invalid professor ID or password.'],
            ]);
        }

        Auth::guard('professor')->login($professor, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('professor.dashboard'));
    }

    /**
     * Handle professor logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('professor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('professor.login');
    }
}