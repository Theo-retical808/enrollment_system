<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            [
                'name' => 'College of Computer Science',
                'code' => 'CS',
                'description' => 'Offers programs in Computer Science, Information Technology, and related fields.',
                'is_active' => true,
            ],
            [
                'name' => 'College of Engineering',
                'code' => 'ENG',
                'description' => 'Offers various engineering programs including Civil, Mechanical, and Electrical Engineering.',
                'is_active' => true,
            ],
            [
                'name' => 'College of Business Administration',
                'code' => 'BUS',
                'description' => 'Offers business and management programs.',
                'is_active' => true,
            ],
            [
                'name' => 'College of Arts and Sciences',
                'code' => 'AS',
                'description' => 'Offers liberal arts and sciences programs.',
                'is_active' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}