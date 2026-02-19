<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\EnrollmentAuditLog;
use App\Services\PerformanceMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditReportController extends Controller
{
    /**
     * Get audit trail for a specific enrollment.
     */
    public function getEnrollmentAuditTrail($enrollmentId)
    {
        $enrollment = Enrollment::with(['student', 'professor', 'courses'])
            ->findOrFail($enrollmentId);

        $auditLogs = EnrollmentAuditLog::getEnrollmentHistory($enrollment);

        return response()->json([
            'success' => true,
            'enrollment' => [
                'id' => $enrollment->id,
                'student' => [
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->full_name,
                    'student_id' => $enrollment->student->student_id,
                ],
                'status' => $enrollment->status,
                'semester' => $enrollment->semester,
                'academic_year' => $enrollment->academic_year,
            ],
            'audit_trail' => $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user_type' => $log->user_type,
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                    'comments' => $log->comments,
                    'metadata' => $log->metadata,
                    'timestamp' => $log->action_timestamp->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Get audit report for all enrollments within a date range.
     */
    public function getAuditReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'action' => 'nullable|string',
            'user_type' => 'nullable|string|in:student,professor,system',
        ]);

        $query = EnrollmentAuditLog::with(['enrollment.student', 'enrollment.professor']);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('action_timestamp', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('action_timestamp', '<=', $request->end_date);
        }

        // Filter by action type
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user type
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        $auditLogs = $query->orderBy('action_timestamp', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'audit_logs' => $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'enrollment_id' => $log->enrollment_id,
                    'student' => [
                        'name' => $log->enrollment->student->full_name ?? 'N/A',
                        'student_id' => $log->enrollment->student->student_id ?? 'N/A',
                    ],
                    'action' => $log->action,
                    'user_type' => $log->user_type,
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                    'comments' => $log->comments,
                    'metadata' => $log->metadata,
                    'timestamp' => $log->action_timestamp->format('Y-m-d H:i:s'),
                ];
            }),
            'pagination' => [
                'current_page' => $auditLogs->currentPage(),
                'total_pages' => $auditLogs->lastPage(),
                'total_records' => $auditLogs->total(),
                'per_page' => $auditLogs->perPage(),
            ],
        ]);
    }

    /**
     * Get audit statistics summary.
     */
    public function getAuditStatistics(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = EnrollmentAuditLog::query();

        if ($request->has('start_date')) {
            $query->where('action_timestamp', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('action_timestamp', '<=', $request->end_date);
        }

        $statistics = [
            'total_actions' => $query->count(),
            'actions_by_type' => $query->select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->pluck('count', 'action'),
            'actions_by_user_type' => $query->select('user_type', DB::raw('count(*) as count'))
                ->groupBy('user_type')
                ->pluck('count', 'user_type'),
            'status_transitions' => $query->select('old_status', 'new_status', DB::raw('count(*) as count'))
                ->whereNotNull('old_status')
                ->whereNotNull('new_status')
                ->groupBy('old_status', 'new_status')
                ->get()
                ->map(function ($item) {
                    return [
                        'from' => $item->old_status,
                        'to' => $item->new_status,
                        'count' => $item->count,
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Export audit report as CSV.
     */
    public function exportAuditReport(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = EnrollmentAuditLog::with(['enrollment.student']);

        if ($request->has('start_date')) {
            $query->where('action_timestamp', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('action_timestamp', '<=', $request->end_date);
        }

        $auditLogs = $query->orderBy('action_timestamp', 'desc')->get();

        $csvData = "ID,Enrollment ID,Student Name,Student ID,Action,User Type,Old Status,New Status,Comments,Timestamp\n";

        foreach ($auditLogs as $log) {
            $csvData .= sprintf(
                "%d,%d,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->enrollment_id,
                $log->enrollment->student->full_name ?? 'N/A',
                $log->enrollment->student->student_id ?? 'N/A',
                $log->action,
                $log->user_type ?? 'N/A',
                $log->old_status ?? 'N/A',
                $log->new_status ?? 'N/A',
                str_replace(["\n", "\r", ","], [" ", " ", ";"], $log->comments ?? ''),
                $log->action_timestamp->format('Y-m-d H:i:s')
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="audit_report_' . now()->format('Y-m-d') . '.csv"');
    }

    /**
     * Get system health and performance metrics.
     */
    public function getSystemHealth()
    {
        try {
            $health = PerformanceMonitor::getHealthMetrics();
            $stats = PerformanceMonitor::getEnrollmentStats();

            return response()->json([
                'success' => true,
                'health' => $health,
                'enrollment_stats' => $stats,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve system health', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve system health metrics.',
            ], 500);
        }
    }

    /**
     * Get error logs summary.
     */
    public function getErrorLogs(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'level' => 'nullable|string|in:error,warning,critical',
        ]);

        try {
            $logFile = storage_path('logs/enrollment.log');
            
            if (!file_exists($logFile)) {
                return response()->json([
                    'success' => true,
                    'errors' => [],
                    'message' => 'No error logs found.',
                ]);
            }

            $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $errors = [];

            foreach (array_reverse($logs) as $line) {
                if (count($errors) >= 100) break;

                if (preg_match('/\[(.*?)\] (\w+)\.(\w+): (.*)/', $line, $matches)) {
                    $timestamp = $matches[1];
                    $level = $matches[3];
                    $message = $matches[4];

                    if ($request->has('level') && strtolower($level) !== strtolower($request->level)) {
                        continue;
                    }

                    $errors[] = [
                        'timestamp' => $timestamp,
                        'level' => $level,
                        'message' => $message,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'errors' => $errors,
                'total' => count($errors),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve error logs.',
            ], 500);
        }
    }
}
