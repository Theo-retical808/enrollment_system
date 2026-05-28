<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CurriculumTemplate;
use App\Models\School;

class CurriculumTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $csSchool = School::where('code', 'CS')->first();

        if (!$csSchool) {
            $this->command->warn('CS school not found, skipping curriculum templates.');
            return;
        }

        // Current semester (May = 2nd Semester based on PaymentVerificationService logic)
        // month >= 11 || month <= 3 => 2nd Semester
        // month >= 6 && month <= 10 => 1st Semester
        // month 4-5 => Summer
        // May = Summer, so let's seed both semesters for completeness

        // CS Curriculum: [year_level, semester, course_code]
        $curriculum = [
            // ─── Year 1, 1st Semester ────────────────────────
            [1, '1st Semester', 'CS101'],
            [1, '1st Semester', 'MATH101'],
            [1, '1st Semester', 'ENGL101'],
            [1, '1st Semester', 'PE101'],
            [1, '1st Semester', 'HIST101'],

            // ─── Year 1, 2nd Semester ────────────────────────
            [1, '2nd Semester', 'CS201'],
            [1, '2nd Semester', 'PHYS101'],
            [1, '2nd Semester', 'CHEM101'],
            [1, '2nd Semester', 'ENGL201'],

            // ─── Year 2, 1st Semester ────────────────────────
            [2, '1st Semester', 'CS202'],
            [2, '1st Semester', 'MATH201'],
            [2, '1st Semester', 'STAT101'],
            [2, '1st Semester', 'BUS101'],
            [2, '1st Semester', 'ACCT101'],

            // ─── Year 2, 2nd Semester ────────────────────────
            [2, '2nd Semester', 'CS301'],
            [2, '2nd Semester', 'CS302'],
            [2, '2nd Semester', 'ECON101'],

            // ─── Year 3, 1st Semester ────────────────────────
            [3, '1st Semester', 'BUS201'],
            [3, '1st Semester', 'BUS202'],
            [3, '1st Semester', 'DRAW101'],

            // ─── Year 3, 2nd Semester ────────────────────────
            [3, '2nd Semester', 'ENGR101'],
            [3, '2nd Semester', 'ENG201'],
            [3, '2nd Semester', 'ENG202'],
        ];

        $created = 0;

        foreach ($curriculum as [$yearLevel, $semester, $courseCode]) {
            $course = Course::where('course_code', $courseCode)->first();
            if (!$course) {
                continue;
            }

            // Find a matching schedule for this course (link it so regulars get real schedules)
            $schedule = CourseSchedule::where('course_id', $course->id)
                ->where('is_active', true)
                ->first();

            CurriculumTemplate::create([
                'school_id' => $csSchool->id,
                'year_level' => $yearLevel,
                'semester' => $semester,
                'course_id' => $course->id,
                'course_schedule_id' => $schedule?->id,
                'is_active' => true,
            ]);

            $created++;
        }

        $this->command->info("Curriculum templates seeded: {$created} entries for {$csSchool->name}.");
        $this->command->info("  Year 1: 1st Sem (5 courses), 2nd Sem (4 courses)");
        $this->command->info("  Year 2: 1st Sem (5 courses), 2nd Sem (3 courses)");
        $this->command->info("  Year 3: 1st Sem (3 courses), 2nd Sem (3 courses)");
        $this->command->info("  Regular students will auto-receive these schedules on enrollment.");
    }
}
