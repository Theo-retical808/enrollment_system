<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\School;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        $csSchool = $schools->where('code', 'CS')->first();
        $engSchool = $schools->where('code', 'ENG')->first();
        $busSchool = $schools->where('code', 'BUS')->first();

        $students = [
            // Regular students (no failed courses)
            [
                'student_id' => '2024-001',
                'email' => 'john.doe@student.edu',
                'password' => Hash::make('password'),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'school_id' => $csSchool->id,
                'year_level' => 2,
                'status' => 'active',
                'type' => 'regular',
            ],
            [
                'student_id' => '2024-002',
                'email' => 'jane.smith@student.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'school_id' => $engSchool->id,
                'year_level' => 1,
                'status' => 'active',
                'type' => 'regular',
            ],
            
            // Irregular students (have failed courses)
            [
                'student_id' => '2024-003',
                'email' => 'bob.wilson@student.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Bob',
                'last_name' => 'Wilson',
                'school_id' => $csSchool->id,
                'year_level' => 3,
                'status' => 'active',
                'type' => 'irregular',
            ],
            [
                'student_id' => '2024-004',
                'email' => 'alice.brown@student.edu',
                'password' => Hash::make('password'),
                'first_name' => 'Alice',
                'last_name' => 'Brown',
                'school_id' => $busSchool->id,
                'year_level' => 2,
                'status' => 'active',
                'type' => 'irregular',
            ],
        ];

        foreach ($students as $studentData) {
            $type = $studentData['type'];
            unset($studentData['type']);
            
            $student = Student::create($studentData);
            
            // Create payment record (paid)
            Payment::create([
                'student_id' => $student->id,
                'payment_type' => 'enrollment_fee',
                'amount' => 5000.00,
                'status' => 'paid',
                'semester' => '2nd Semester',
                'academic_year' => '2025-2026',
                'paid_at' => now(),
            ]);
            
            // Add completed courses based on student type
            $this->addCompletedCourses($student, $type);
        }
    }

    private function addCompletedCourses(Student $student, string $type): void
    {
        $courses = Course::all();
        
        if ($type === 'regular') {
            // Regular students have passed all their courses
            $completedCourses = [];
            
            if ($student->school->code === 'CS') {
                if ($student->year_level >= 2) {
                    $completedCourses = ['CS101', 'MATH101', 'ENGL101', 'HIST101', 'PE101'];
                }
            } elseif ($student->school->code === 'ENG') {
                if ($student->year_level >= 2) {
                    $completedCourses = ['ENGR101', 'MATH101', 'PHYS101', 'CHEM101', 'DRAW101'];
                }
            }
            
            foreach ($completedCourses as $courseCode) {
                $course = $courses->where('course_code', $courseCode)->first();
                if ($course) {
                    $student->completedCourses()->attach($course->id, [
                        'grade' => 'A',
                        'semester' => '1st Semester',
                        'academic_year' => '2024-2025',
                        'passed' => true,
                    ]);
                }
            }
        } else {
            // Irregular students have some failed courses
            $completedCourses = [];
            $failedCourses = [];
            
            if ($student->school->code === 'CS') {
                $completedCourses = ['MATH101', 'ENGL101', 'HIST101'];
                $failedCourses = ['CS101', 'PE101']; // Failed these courses
            } elseif ($student->school->code === 'BUS') {
                $completedCourses = ['ENGL101', 'MATH101'];
                $failedCourses = ['BUS101', 'ECON101']; // Failed these courses
            }
            
            // Add passed courses
            foreach ($completedCourses as $courseCode) {
                $course = $courses->where('course_code', $courseCode)->first();
                if ($course) {
                    $student->completedCourses()->attach($course->id, [
                        'grade' => 'B',
                        'semester' => '1st Semester',
                        'academic_year' => '2024-2025',
                        'passed' => true,
                    ]);
                }
            }
            
            // Add failed courses
            foreach ($failedCourses as $courseCode) {
                $course = $courses->where('course_code', $courseCode)->first();
                if ($course) {
                    $student->completedCourses()->attach($course->id, [
                        'grade' => 'F',
                        'semester' => '1st Semester',
                        'academic_year' => '2024-2025',
                        'passed' => false,
                    ]);
                }
            }
        }
    }
}