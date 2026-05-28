<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'professor_id',
        'day',
        'start_time',
        'end_time',
        'room',
        'max_students',
        'enrolled_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    /**
     * Get the course for this schedule.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the professor assigned to this schedule.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    /**
     * Check if there are available slots.
     */
    public function hasAvailableSlots(): bool
    {
        return $this->enrolled_count < $this->max_students;
    }

    /**
     * Get available slots count.
     */
    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_students - $this->enrolled_count);
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return date('g:i A', strtotime($this->start_time)) . ' - ' . date('g:i A', strtotime($this->end_time));
    }
}
