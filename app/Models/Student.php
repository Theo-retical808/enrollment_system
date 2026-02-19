<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'student_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'school_id',
        'year_level',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Boot method to clear cache when student data changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($student) {
            Cache::forget("student_completed_courses_{$student->id}");
            Cache::forget("student_is_regular_{$student->id}");
            Cache::forget("student_has_failed_courses_{$student->id}");
        });

        static::deleted(function ($student) {
            Cache::forget("student_completed_courses_{$student->id}");
            Cache::forget("student_is_regular_{$student->id}");
            Cache::forget("student_has_failed_courses_{$student->id}");
        });
    }

    /**
     * Get the school that owns the student.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the completed courses for the student.
     */
    public function completedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'student_completed_courses')
            ->withPivot(['grade', 'semester', 'academic_year', 'passed'])
            ->withTimestamps();
    }

    /**
     * Get the payments for the student.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the petitions for the student.
     */
    public function petitions(): HasMany
    {
        return $this->hasMany(Petition::class);
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if the student is regular (no failed courses).
     * Uses caching for improved performance.
     */
    public function isRegular(): bool
    {
        $cacheKey = "student_is_regular_{$this->id}";
        
        return Cache::remember($cacheKey, 3600, function () {
            return !$this->hasFailedCourses();
        });
    }

    /**
     * Check if the student is irregular (has failed courses).
     */
    public function isIrregular(): bool
    {
        return !$this->isRegular();
    }

    /**
     * Check if the student has failed courses.
     * Uses caching for improved performance.
     */
    public function hasFailedCourses(): bool
    {
        $cacheKey = "student_has_failed_courses_{$this->id}";
        
        return Cache::remember($cacheKey, 3600, function () {
            return $this->completedCourses()
                ->wherePivot('passed', false)
                ->exists();
        });
    }

    /**
     * Get the current enrollment for the student.
     */
    public function getCurrentEnrollment(): ?Enrollment
    {
        return $this->enrollments()
            ->with('courses')
            ->where('status', '!=', 'rejected')
            ->latest()
            ->first();
    }

    /**
     * Check if enrollment fee is paid for current semester.
     */
    public function hasEnrollmentFeePaid(string $semester, string $academicYear): bool
    {
        return $this->payments()
            ->where('payment_type', 'enrollment_fee')
            ->where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->where('status', 'paid')
            ->exists();
    }
}