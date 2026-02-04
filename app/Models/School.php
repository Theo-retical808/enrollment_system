<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the students for the school.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the professors for the school.
     */
    public function professors(): HasMany
    {
        return $this->hasMany(Professor::class);
    }

    /**
     * Get the courses for the school.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}