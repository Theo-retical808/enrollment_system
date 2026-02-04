<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Petition extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'justification',
        'status',
        'review_comments',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the student that owns the petition.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for the petition.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the professor who reviewed the petition.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'reviewed_by');
    }

    /**
     * Approve the petition.
     */
    public function approve(Professor $professor, string $comments = ''): void
    {
        $this->update([
            'status' => 'approved',
            'review_comments' => $comments,
            'reviewed_by' => $professor->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Reject the petition.
     */
    public function reject(Professor $professor, string $comments): void
    {
        $this->update([
            'status' => 'rejected',
            'review_comments' => $comments,
            'reviewed_by' => $professor->id,
            'reviewed_at' => now(),
        ]);
    }
}