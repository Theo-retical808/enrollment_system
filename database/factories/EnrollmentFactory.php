<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'semester' => $this->faker->randomElement(['Fall', 'Spring', 'Summer']),
            'academic_year' => $this->faker->randomElement(['2023-2024', '2024-2025', '2025-2026']),
            'status' => 'draft',
            'total_units' => 0,
            'professor_id' => null,
            'review_comments' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'professor_id' => Professor::factory(),
            'submitted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'professor_id' => Professor::factory(),
            'submitted_at' => $this->faker->dateTimeBetween('-1 month', '-1 week'),
            'reviewed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}