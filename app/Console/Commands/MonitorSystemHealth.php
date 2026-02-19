<?php

namespace App\Console\Commands;

use App\Services\PerformanceMonitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorSystemHealth extends Command
{
    protected $signature = 'enrollment:monitor-health';
    protected $description = 'Monitor enrollment system health and log metrics';

    public function handle()
    {
        $this->info('Checking system health...');

        try {
            $health = PerformanceMonitor::getHealthMetrics();
            $stats = PerformanceMonitor::getEnrollmentStats();

            // Log health metrics
            Log::channel('audit')->info('System health check', [
                'health' => $health,
                'stats' => $stats,
            ]);

            // Display results
            $this->info('Database: ' . $health['database']['status']);
            $this->info('Cache: ' . $health['cache']['status']);
            $this->info('Disk Space: ' . $health['disk_space']['used_percentage'] . '% used');
            $this->info('Memory: ' . $health['memory_usage']['current']);
            
            $this->newLine();
            $this->info('Enrollment Statistics:');
            $this->info('Active Enrollments: ' . $stats['active_enrollments']);
            $this->info('Pending Reviews: ' . $stats['pending_reviews']);
            $this->info('Approved Today: ' . $stats['approved_today']);

            // Check for warnings
            if ($health['disk_space']['status'] === 'warning') {
                $this->warn('Warning: Disk space usage is high!');
            }

            if ($stats['pending_reviews'] > 50) {
                $this->warn('Warning: High number of pending reviews!');
            }

            $this->info('Health check completed successfully.');
            return 0;

        } catch (\Exception $e) {
            $this->error('Health check failed: ' . $e->getMessage());
            Log::error('System health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }
}
