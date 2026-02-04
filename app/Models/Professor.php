<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'professor_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'school_id',
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
     * Get the school that owns the professor.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the enrollments reviewed by the professor.
     */
    public function reviewedEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the petitions reviewed by the professor.
     */
    public function reviewedPetitions(): HasMany
    {
        return $this->hasMany(Petition::class, 'reviewed_by');
    }

    /**
     * Get the professor's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}