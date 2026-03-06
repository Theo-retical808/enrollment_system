<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Payment;

class AdditionalPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        
        // Add historical payments for each student
        foreach ($students as $student) {
            // Previous semester payment (1st Semester 2025-2026)
            Payment::create([
                'student_id' => $student->id,
                'semester' => '1st Semester',
                'academic_year' => '2025-2026',
                'amount' => rand(15000, 25000),
                'status' => 'paid',
                'paid_at' => now()->subMonths(6),
                'created_at' => now()->subMonths(6),
            ]);
            
            // Previous year payment (2nd Semester 2024-2025)
            Payment::create([
                'student_id' => $student->id,
                'semester' => '2nd Semester',
                'academic_year' => '2024-2025',
                'amount' => rand(15000, 25000),
                'status' => 'paid',
                'paid_at' => now()->subYear(),
                'created_at' => now()->subYear(),
            ]);
            
            // Previous year payment (1st Semester 2024-2025)
            Payment::create([
                'student_id' => $student->id,
                'semester' => '1st Semester',
                'academic_year' => '2024-2025',
                'amount' => rand(15000, 25000),
                'status' => 'paid',
                'paid_at' => now()->subYear()->subMonths(6),
                'created_at' => now()->subYear()->subMonths(6),
            ]);
        }
        
        $this->command->info('Additional payment records created successfully!');
    }
}
