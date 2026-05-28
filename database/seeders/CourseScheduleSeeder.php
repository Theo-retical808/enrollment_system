<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Professor;

class CourseScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $professors = Professor::all()->keyBy('professor_id');

        // Schedules using only existing courses:
        // CS101, CS201, CS202, CS301, CS302, MATH101, MATH201, ENGL101, ENGL201,
        // ENG201, ENG202, ENGR101, PE101, HIST101, PHYS101, CHEM101, ACCT101,
        // BUS101, BUS201, BUS202, ECON101, STAT101, DRAW101
        $schedules = [
            // ─── PROF001 (John Smith - CS, Enrollment Assistant) ─────────
            ['CS101', 'PROF001', 'Monday', '08:00', '09:30', 'CS-101'],
            ['CS201', 'PROF001', 'Monday', '10:00', '11:30', 'CS-102'],
            ['CS301', 'PROF001', 'Tuesday', '08:00', '09:30', 'CS-103'],
            ['CS202', 'PROF001', 'Wednesday', '08:00', '09:30', 'CS-104'],
            ['CS302', 'PROF001', 'Thursday', '08:00', '09:30', 'CS-105'],

            // ─── PROF002 (Sarah Johnson - Engineering, Enrollment Assistant) ─
            ['ENGR101', 'PROF002', 'Monday', '08:00', '09:30', 'ENG-201'],
            ['ENG201', 'PROF002', 'Tuesday', '10:00', '11:30', 'ENG-202'],
            ['ENG202', 'PROF002', 'Wednesday', '10:00', '11:30', 'ENG-203'],
            ['DRAW101', 'PROF002', 'Thursday', '13:00', '14:30', 'ENG-204'],

            // ─── PROF003 (Michael Davis - Business, Enrollment Assistant) ─
            ['BUS101', 'PROF003', 'Monday', '13:00', '14:30', 'BUS-301'],
            ['BUS201', 'PROF003', 'Tuesday', '13:00', '14:30', 'BUS-302'],
            ['BUS202', 'PROF003', 'Wednesday', '13:00', '14:30', 'BUS-303'],
            ['ACCT101', 'PROF003', 'Thursday', '10:00', '11:30', 'BUS-304'],
            ['ECON101', 'PROF003', 'Friday', '08:00', '09:30', 'BUS-305'],

            // ─── PROF004 (Emily Wilson - Arts & Sciences, NOT assistant) ─
            ['MATH101', 'PROF004', 'Monday', '08:00', '09:30', 'AS-401'],
            ['MATH201', 'PROF004', 'Tuesday', '08:00', '09:30', 'AS-402'],
            ['HIST101', 'PROF004', 'Wednesday', '08:00', '09:30', 'AS-403'],
            ['STAT101', 'PROF004', 'Thursday', '08:00', '09:30', 'AS-404'],
            ['PE101', 'PROF004', 'Friday', '10:00', '11:30', 'Gym A'],

            // ─── PROF005 (Carlos Garcia - CS, NOT assistant) ─────────────
            ['CS101', 'PROF005', 'Wednesday', '10:00', '11:30', 'CS-106'],
            ['CS201', 'PROF005', 'Thursday', '10:00', '11:30', 'CS-107'],
            ['CS301', 'PROF005', 'Friday', '13:00', '14:30', 'CS-108'],

            // ─── PROF006 (Ana Martinez - Engineering, NOT assistant) ─────
            ['ENGL101', 'PROF006', 'Monday', '10:00', '11:30', 'ENG-205'],
            ['ENGL201', 'PROF006', 'Tuesday', '10:00', '11:30', 'ENG-206'],
            ['PHYS101', 'PROF006', 'Wednesday', '13:00', '14:30', 'SCI-501'],
            ['CHEM101', 'PROF006', 'Thursday', '13:00', '14:30', 'SCI-502'],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($schedules as $data) {
            [$courseCode, $profId, $day, $start, $end, $room] = $data;

            $course = Course::where('course_code', $courseCode)->first();
            $professor = $professors->get($profId);

            if (!$course || !$professor) {
                $this->command->warn("Skipped: {$courseCode} / {$profId} - not found");
                $skipped++;
                continue;
            }

            CourseSchedule::create([
                'course_id' => $course->id,
                'professor_id' => $professor->id,
                'day' => $day,
                'start_time' => $start,
                'end_time' => $end,
                'room' => $room,
                'max_students' => 40,
                'enrolled_count' => 0,
                'is_active' => true,
            ]);

            $created++;
        }

        $this->command->info("Course schedules seeded: {$created} created, {$skipped} skipped.");
    }
}
