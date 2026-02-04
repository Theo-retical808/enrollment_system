<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'title',
        'description',
        'units',
        'school_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns the course.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the prerequisites for the course.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id')
            ->withTimestamps();
    }

    /**
     * Get the courses that have this course as a prerequisite.
     */
    public function dependentCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_id', 'course_id')
            ->withTimestamps();
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): BelongsToMany
    {
        return $this->belongsToMany(Enrollment::class, 'enrollment_courses')
            ->withPivot(['schedule_day', 'start_time', 'end_time', 'room', 'instructor'])
            ->withTimestamps();
    }

    /**
     * Get the students who completed this course.
     */
    public function completedByStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_completed_courses')
            ->withPivot(['grade', 'semester', 'academic_year', 'passed'])
            ->withTimestamps();
    }

    /**
     * Get the petitions for this course.
     */
    public function petitions(): HasMany
    {
        return $this->hasMany(Petition::class);
    }

    /**
     * Check if prerequisites are satisfied by a student.
     */
    public function hasPrerequisitesSatisfiedBy(Student $student): bool
    {
        $prerequisites = $this->prerequisites;
        
        if ($prerequisites->isEmpty()) {
            return true;
        }

        $completedCourseIds = $student->completedCourses()
            ->wherePivot('passed', true)
            ->pluck('courses.id')
            ->toArray();

        foreach ($prerequisites as $prerequisite) {
            if (!in_array($prerequisite->id, $completedCourseIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get available schedule slots for the course.
     */
    public function getAvailableSlots(): Collection
    {
        // This would typically come from a course_schedules table
        // For now, return a basic collection
        return collect([]);
    }
}