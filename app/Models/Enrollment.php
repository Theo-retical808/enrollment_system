<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'semester',
        'academic_year',
        'status',
        'total_units',
        'professor_id',
        'review_comments',
        'submitted_at',
        'reviewed_at',
        'validation_data',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'validation_data' => 'array',
    ];

    /**
     * Get the student that owns the enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the professor assigned to review the enrollment.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    /**
     * Get the courses for the enrollment.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollment_courses')
            ->withPivot(['schedule_day', 'start_time', 'end_time', 'room', 'instructor'])
            ->withTimestamps();
    }

    /**
     * Get the audit logs for the enrollment.
     */
    public function auditLogs()
    {
        return $this->hasMany(EnrollmentAuditLog::class);
    }

    /**
     * Get the total units for the enrollment.
     */
    public function getTotalUnits(): int
    {
        return $this->courses()->sum('units');
    }

    /**
     * Check if the enrollment has schedule conflicts.
     */
    public function hasScheduleConflicts(): bool
    {
        $courses = $this->courses()->get();
        
        for ($i = 0; $i < $courses->count(); $i++) {
            for ($j = $i + 1; $j < $courses->count(); $j++) {
                $course1 = $courses[$i];
                $course2 = $courses[$j];
                
                // Check if same day
                if ($course1->pivot->schedule_day === $course2->pivot->schedule_day) {
                    $start1 = Carbon::parse($course1->pivot->start_time);
                    $end1 = Carbon::parse($course1->pivot->end_time);
                    $start2 = Carbon::parse($course2->pivot->start_time);
                    $end2 = Carbon::parse($course2->pivot->end_time);
                    
                    // Check for time overlap
                    if ($start1->lt($end2) && $start2->lt($end1)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Check if a course can be added to the enrollment.
     */
    public function canAddCourse(Course $course, array $scheduleData): bool
    {
        // Check unit load limit (21 units max)
        if ($this->getTotalUnits() + $course->units > 21) {
            return false;
        }

        // Check prerequisites
        if (!$course->hasPrerequisitesSatisfiedBy($this->student)) {
            return false;
        }

        // Check schedule conflicts
        $tempCourse = (object) [
            'pivot' => (object) $scheduleData
        ];
        
        foreach ($this->courses as $existingCourse) {
            if ($existingCourse->pivot->schedule_day === $tempCourse->pivot->schedule_day) {
                $existingStart = Carbon::parse($existingCourse->pivot->start_time);
                $existingEnd = Carbon::parse($existingCourse->pivot->end_time);
                $newStart = Carbon::parse($tempCourse->pivot->start_time);
                $newEnd = Carbon::parse($tempCourse->pivot->end_time);
                
                if ($existingStart->lt($newEnd) && $newStart->lt($existingEnd)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Submit the enrollment for approval.
     */
    public function submitForApproval(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'total_units' => $this->getTotalUnits(),
        ]);

        return true;
    }

    /**
     * Finalize the enrollment after approval.
     * This locks the schedule and prevents further modifications.
     */
    public function finalize(): void
    {
        // Mark as finalized in validation_data
        $validationData = $this->validation_data ?? [];
        $validationData['finalized_at'] = now()->toIso8601String();
        $validationData['is_locked'] = true;
        
        $this->update([
            'validation_data' => $validationData,
        ]);
    }

    /**
     * Enable resubmission after rejection.
     * This allows the student to return to planning and resubmit.
     */
    public function enableResubmission(): void
    {
        // Reset to draft status to allow modifications
        $this->update([
            'status' => 'draft',
            'submitted_at' => null,
        ]);
    }

    /**
     * Check if the enrollment is finalized and locked.
     */
    public function isFinalized(): bool
    {
        $validationData = $this->validation_data ?? [];
        return isset($validationData['is_locked']) && $validationData['is_locked'] === true;
    }

    /**
     * Check if the enrollment can be resubmitted.
     */
    public function canResubmit(): bool
    {
        return $this->status === 'draft' && $this->reviewed_at !== null;
    }
}