<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    private static $timers = [];

    /**
     * Start a performance timer.
     */
    public static function start(string $operation): void
    {
        self::$timers[$operation] = microtime(true);
    }

    /**
     * Stop a performance timer and log the duration.
     */
    public static function stop(string $operation, array $context = []): float
    {
        if (!isset(self::$timers[$operation])) {
            return 0.0;
        }

        $duration = microtime(true) - self::$timers[$operation];
        unset(self::$timers[$operation]);

        EnrollmentLogger::logPerformance($operation, $duration, $context);

        // Track slow operations
        if ($duration > 1.0) {
            self::recordSlowOperation($operation, $duration, $context);
        }

        return $duration;
    }

    /**
     * Record slow operations for monitoring.
     */
    private static function recordSlowOperation(string $operation, float $duration, array $context): void
    {
        $key = 'slow_operations:' . date('Y-m-d');
        $data = Cache::get($key, []);
        
        $data[] = [
            'operation' => $operation,
            'duration' => $duration,
            'context' => $context,
            'timestamp' => now()->toIso8601String(),
        ];

        Cache::put($key, $data, now()->addDays(7));
    }

    /**
     * Get system health metrics.
     */
    public static function getHealthMetrics(): array
    {
        return [
            'database' => self::checkDatabaseHealth(),
            'cache' => self::checkCacheHealth(),
            'disk_space' => self::checkDiskSpace(),
            'memory_usage' => self::getMemoryUsage(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Check database health.
     */
    private static function checkDatabaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $duration = microtime(true) - $start;

            return [
                'status' => 'healthy',
                'response_time' => round($duration * 1000, 2) . 'ms',
            ];
        } catch (\Exception $e) {
            Log::error('Database health check failed', ['error' => $e->getMessage()]);
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache health.
     */
    private static function checkCacheHealth(): array
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            return [
                'status' => $value === 'test' ? 'healthy' : 'degraded',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check disk space.
     */
    private static function checkDiskSpace(): array
    {
        $path = storage_path();
        $free = disk_free_space($path);
        $total = disk_total_space($path);
        $used = $total - $free;
        $percentage = ($used / $total) * 100;

        return [
            'free' => self::formatBytes($free),
            'total' => self::formatBytes($total),
            'used_percentage' => round($percentage, 2),
            'status' => $percentage > 90 ? 'warning' : 'healthy',
        ];
    }

    /**
     * Get memory usage.
     */
    private static function getMemoryUsage(): array
    {
        $usage = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);

        return [
            'current' => self::formatBytes($usage),
            'peak' => self::formatBytes($peak),
        ];
    }

    /**
     * Format bytes to human-readable format.
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get enrollment system statistics.
     */
    public static function getEnrollmentStats(): array
    {
        return [
            'active_enrollments' => DB::table('enrollments')
                ->where('status', 'draft')
                ->count(),
            'pending_reviews' => DB::table('enrollments')
                ->where('status', 'submitted')
                ->count(),
            'approved_today' => DB::table('enrollments')
                ->where('status', 'approved')
                ->whereDate('reviewed_at', today())
                ->count(),
            'total_students' => DB::table('students')
                ->where('status', 'active')
                ->count(),
        ];
    }
}
