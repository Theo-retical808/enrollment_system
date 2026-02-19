<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentAuditLog extends Model
{
    protected $fillable = [
        'enrollment_id',
        'user_id',
        'user_type',
        'action',
        'old_status',
        'new_status',
        'comments',
        'metadata',
        'action_timestamp',
    ];

    protected $casts = [
        'metadata' => 'array',
        'action_timestamp' => 'datetime',
    ];

    /**
     * Get the enrollment that this audit log belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Create an audit log entry for an enrollment action.
     */
    public static function logAction(
        Enrollment $enrollment,
        string $action,
        ?int $userId = null,
        ?string $userType = null,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $comments = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'enrollment_id' => $enrollment->id,
            'user_id' => $userId,
            'user_type' => $userType,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comments' => $comments,
            'metadata' => $metadata,
            'action_timestamp' => now(),
        ]);
    }

    /**
     * Get audit logs for a specific enrollment.
     */
    public static function getEnrollmentHistory(Enrollment $enrollment)
    {
        return self::where('enrollment_id', $enrollment->id)
            ->orderBy('action_timestamp', 'desc')
            ->get();
    }

    /**
     * Get audit logs for a specific action type.
     */
    public static function getActionLogs(string $action)
    {
        return self::where('action', $action)
            ->orderBy('action_timestamp', 'desc')
            ->get();
    }
}
