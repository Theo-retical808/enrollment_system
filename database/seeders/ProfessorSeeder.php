<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professor;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class ProfessorSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();

        $professors = [
            // Professors designated to assist enrollment
            [
                'professor_id' => 'PROF001',
                'email' => 'prof.smith@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'John',
                'last_name' => 'Smith',
                'school_id' => $schools->where('code', 'CS')->first()->id,
                'status' => 'active',
                'can_assist_enrollment' => true,
            ],
            [
                'professor_id' => 'PROF002',
                'email' => 'prof.johnson@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'school_id' => $schools->where('code', 'ENG')->first()->id,
                'status' => 'active',
                'can_assist_enrollment' => true,
            ],
            [
                'professor_id' => 'PROF003',
                'email' => 'prof.davis@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Michael',
                'last_name' => 'Davis',
                'school_id' => $schools->where('code', 'BUS')->first()->id,
                'status' => 'active',
                'can_assist_enrollment' => true,
            ],
            // Professors NOT designated to assist enrollment
            [
                'professor_id' => 'PROF004',
                'email' => 'prof.wilson@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Emily',
                'last_name' => 'Wilson',
                'school_id' => $schools->where('code', 'AS')->first()->id,
                'status' => 'active',
                'can_assist_enrollment' => false,
            ],
            [
                'professor_id' => 'PROF005',
                'email' => 'prof.garcia@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Carlos',
                'last_name' => 'Garcia',
                'school_id' => $schools->where('code', 'CS')->first()->id,
                'status' => 'active',
                'can_assist_enrollment' => false,
            ],
            [
                'professor_id' => 'PROF006',
                'email' => 'prof.martinez@university.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Ana',
                'last_name' => 'Martinez',
                'school_id' => $schools->where('code', 'ENG')->first()->id,
                'status' => 'active',
                'can_assist_enrollment' => false,
            ],
        ];

        foreach ($professors as $professor) {
            Professor::create($professor);
        }

        $this->command->info('Professors seeded: 3 enrollment assistants, 3 regular professors.');
    }
}
