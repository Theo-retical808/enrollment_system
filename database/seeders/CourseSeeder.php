<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\School;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $csSchool = School::where('code', 'CS')->first();
        $engSchool = School::where('code', 'ENG')->first();
        $busSchool = School::where('code', 'BUS')->first();
        $asSchool = School::where('code', 'AS')->first();

        $courses = [
            // Computer Science Courses
            ['course_code' => 'CS101', 'title' => 'Introduction to Programming', 'description' => 'Basic programming concepts', 'units' => 3, 'school_id' => $csSchool->id],
            ['course_code' => 'CS201', 'title' => 'Data Structures and Algorithms', 'description' => 'Advanced programming concepts', 'units' => 3, 'school_id' => $csSchool->id],
            ['course_code' => 'CS202', 'title' => 'Object-Oriented Programming', 'description' => 'OOP principles and practices', 'units' => 3, 'school_id' => $csSchool->id],
            ['course_code' => 'CS301', 'title' => 'Database Systems', 'description' => 'Database design and management', 'units' => 3, 'school_id' => $csSchool->id],
            ['course_code' => 'CS302', 'title' => 'Web Development', 'description' => 'Modern web development techniques', 'units' => 3, 'school_id' => $csSchool->id],

            // Engineering Courses
            ['course_code' => 'ENGR101', 'title' => 'Engineering Drawing', 'description' => 'Technical drawing fundamentals', 'units' => 3, 'school_id' => $engSchool->id],
            ['course_code' => 'ENG201', 'title' => 'Statics', 'description' => 'Engineering mechanics - statics', 'units' => 3, 'school_id' => $engSchool->id],
            ['course_code' => 'ENG202', 'title' => 'Dynamics', 'description' => 'Engineering mechanics - dynamics', 'units' => 3, 'school_id' => $engSchool->id],

            // Business Courses
            ['course_code' => 'BUS101', 'title' => 'Introduction to Business', 'description' => 'Basic business concepts', 'units' => 3, 'school_id' => $busSchool->id],
            ['course_code' => 'BUS201', 'title' => 'Marketing Management', 'description' => 'Marketing principles and strategies', 'units' => 3, 'school_id' => $busSchool->id],
            ['course_code' => 'BUS202', 'title' => 'Financial Management', 'description' => 'Corporate finance principles', 'units' => 3, 'school_id' => $busSchool->id],

            // General Education Courses
            ['course_code' => 'MATH101', 'title' => 'College Algebra', 'description' => 'Fundamental algebra concepts', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'MATH201', 'title' => 'Calculus I', 'description' => 'Differential calculus', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'ENGL101', 'title' => 'English Composition', 'description' => 'Writing and communication skills', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'ENGL201', 'title' => 'Literature', 'description' => 'World literature survey', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'PHYS101', 'title' => 'General Physics I', 'description' => 'Mechanics and thermodynamics', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'CHEM101', 'title' => 'General Chemistry', 'description' => 'Basic chemistry principles', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'HIST101', 'title' => 'World History', 'description' => 'Survey of world history', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'PE101', 'title' => 'Physical Education', 'description' => 'Physical fitness and sports', 'units' => 2, 'school_id' => $asSchool->id],
            ['course_code' => 'ECON101', 'title' => 'Principles of Economics', 'description' => 'Basic economic principles', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'ACCT101', 'title' => 'Principles of Accounting', 'description' => 'Basic accounting principles', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'STAT101', 'title' => 'Statistics', 'description' => 'Statistical methods and analysis', 'units' => 3, 'school_id' => $asSchool->id],
            ['course_code' => 'DRAW101', 'title' => 'Technical Drawing', 'description' => 'Engineering drawing principles', 'units' => 2, 'school_id' => $asSchool->id],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        // Add prerequisites
        $this->addPrerequisites();
    }

    private function addPrerequisites(): void
    {
        $prerequisites = [
            'CS201' => ['CS101'], // Data Structures requires Intro to Programming
            'CS202' => ['CS101'], // OOP requires Intro to Programming
            'CS301' => ['CS201'], // Database Systems requires Data Structures
            'CS302' => ['CS202'], // Web Development requires OOP
            'MATH201' => ['MATH101'], // Calculus requires College Algebra
            'ENG201' => ['ENGR101'], // Statics requires Engineering Drawing
            'ENG202' => ['ENG201'], // Dynamics requires Statics
            'BUS201' => ['BUS101'], // Marketing requires Intro to Business
            'BUS202' => ['BUS101'], // Finance requires Intro to Business
        ];

        foreach ($prerequisites as $courseCode => $prereqCodes) {
            $course = Course::where('course_code', $courseCode)->first();
            if ($course) {
                foreach ($prereqCodes as $prereqCode) {
                    $prereq = Course::where('course_code', $prereqCode)->first();
                    if ($prereq) {
                        $course->prerequisites()->attach($prereq->id);
                    }
                }
            }
        }
    }
}