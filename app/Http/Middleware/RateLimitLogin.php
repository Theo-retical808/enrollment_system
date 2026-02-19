<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitLogin
{
    /**
     * Handle an incoming request.
     * Implements rate limiting for login attempts to prevent brute force attacks.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        // Allow 5 login attempts per minute per IP/user combination
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'TOO_MANY_LOGIN_ATTEMPTS',
                    'message' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
                    'retry_after' => $seconds,
                ], 429);
            }

            return redirect()->back()
                ->withErrors([
                    'student_id' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
                ])
                ->withInput($request->except('password'));
        }

        RateLimiter::hit($key, 300); // 5 minutes decay for failed attempts

        $response = $next($request);

        // Clear rate limit on successful login (2xx response)
        if ($response->isSuccessful() || $response->isRedirection()) {
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Resolve the request signature for rate limiting.
     * Uses IP address and student_id/professor_id if provided.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $identifier = $request->input('student_id') ?? $request->input('professor_id') ?? 'guest';
        
        return 'login_' . $identifier . '_' . $request->ip();
    }
}
