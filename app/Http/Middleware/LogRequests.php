<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PerformanceMonitor;

class LogRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $requestId = uniqid('req_', true);
        $request->attributes->set('request_id', $requestId);

        // Start performance timer
        $startTime = microtime(true);

        // Log incoming request
        Log::channel('enrollment')->info('Incoming request', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth('student')->id() ?? auth('professor')->id(),
            'user_type' => auth('student')->check() ? 'student' : (auth('professor')->check() ? 'professor' : 'guest'),
        ]);

        $response = $next($request);

        // Calculate request duration
        $duration = microtime(true) - $startTime;

        // Log response
        Log::channel('enrollment')->info('Request completed', [
            'request_id' => $requestId,
            'status_code' => $response->getStatusCode(),
            'duration' => round($duration, 3),
            'memory_peak' => memory_get_peak_usage(true),
        ]);

        // Track slow requests
        if ($duration > 2.0) {
            Log::channel('performance')->warning('Slow request detected', [
                'request_id' => $requestId,
                'url' => $request->fullUrl(),
                'duration' => round($duration, 3),
            ]);
        }

        return $response;
    }
}
