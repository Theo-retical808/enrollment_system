<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course_code' => $this->faker->unique()->regexify('[A-Z]{2,4}[0-9]{3}'),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'units' => $this->faker->numberBetween(1, 4),
            'school_id' => School::factory(),
            'is_active' => true,
        ];
    }
}