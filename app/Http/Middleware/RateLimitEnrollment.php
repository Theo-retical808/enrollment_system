<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitEnrollment
{
    /**
     * Handle an incoming request.
     * Implements rate limiting for enrollment operations to prevent abuse.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        // Allow 10 enrollment operations per minute per user
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'RATE_LIMIT_EXCEEDED',
                    'message' => 'Too many enrollment attempts. Please try again in ' . $seconds . ' seconds.',
                    'retry_after' => $seconds,
                ], 429);
            }

            return redirect()->back()
                ->with('error', 'Too many enrollment attempts. Please wait a moment and try again.')
                ->withInput();
        }

        RateLimiter::hit($key, 60); // 60 seconds decay

        $response = $next($request);

        // Clear rate limit on successful operations
        if ($response->isSuccessful()) {
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Resolve the request signature for rate limiting.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'enrollment_' . $user->id . '_' . $request->ip();
        }

        return 'enrollment_guest_' . $request->ip();
    }
}
