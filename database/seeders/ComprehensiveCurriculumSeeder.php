<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\School;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ComprehensiveCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Clear existing test data
            $this->clearExistingData();
            
            // Get schools
            $csSchool = School::where('code', 'CS')->first();
            
            // Create comprehensive test students
            $this->createRegularStudent2ndYear($csSchool);
            $this->createRegularStudent3rdYear($csSchool);
            $this->createIrregularStudent2ndYear($csSchool);
            $this->createIrregularStudent3rdYear($csSchool);
            
            DB::commit();
            $this->command->info('Comprehensive curriculum test data created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating test data: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function clearExistingData(): void
    {
        // Clear only test students (keep the structure)
        DB::table('enrollment_courses')->whereIn('enrollment_id', function($query) {
            $query->select('id')->from('enrollments')
                  ->whereIn('student_id', function($q) {
                      $q->select('id')->from('students')
                        ->whereIn('student_id', ['2024-REG-001', '2024-REG-002', '2024-IRR-001', '2024-IRR-002']);
                  });
        })->delete();
        
        DB::table('enrollments')->whereIn('student_id', function($query) {
            $query->select('id')->from('students')
                  ->whereIn('student_id', ['2024-REG-001', '2024-REG-002', '2024-IRR-001', '2024-IRR-002']);
        })->delete();
        
        DB::table('student_completed_courses')->whereIn('student_id', function($query) {
            $query->select('id')->from('students')
                  ->whereIn('student_id', ['2024-REG-001', '2024-REG-002', '2024-IRR-001', '2024-IRR-002']);
        })->delete();
        
        DB::table('payments')->whereIn('student_id', function($query) {
            $query->select('id')->from('students')
                  ->whereIn('student_id', ['2024-REG-001', '2024-REG-002', '2024-IRR-001', '2024-IRR-002']);
        })->delete();
        
        Student::whereIn('student_id', ['2024-REG-001', '2024-REG-002', '2024-IRR-001', '2024-IRR-002'])->delete();
    }

    
    /**
     * Create a regular 2nd year student with all 1st year courses passed
     * Currently enrolled in 2nd year 2nd semester courses
     */
    private function createRegularStudent2ndYear(School $school): void
    {
        $student = Student::create([
            'student_id' => '2024-REG-001',
            'email' => 'regular.student1@student.edu',
            'password' => Hash::make('password'),
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'school_id' => $school->id,
            'year_level' => 2,
            'status' => 'active',
        ]);
        
        // Payment for current semester
        Payment::create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'amount' => 5000.00,
            'status' => 'paid',
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'paid_at' => now(),
        ]);
        
        // PASSED COURSES - Year 1, 1st Semester (2024-2025)
        $this->attachCompletedCourse($student, 'CS101', 1.75, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'MATH101', 1.50, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'ENGL101', 1.25, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'PE101', 1.00, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'HIST101', 2.00, '1st Semester', '2024-2025', true);
        
        // PASSED COURSES - Year 1, 2nd Semester (2024-2025)
        $this->attachCompletedCourse($student, 'PHYS101', 1.75, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'CHEM101', 2.00, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'ENGL201', 1.50, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'STAT101', 1.75, '2nd Semester', '2024-2025', true);
        
        // PASSED COURSES - Year 2, 1st Semester (2025-2026)
        $this->attachCompletedCourse($student, 'CS201', 1.50, '1st Semester', '2025-2026', true);
        $this->attachCompletedCourse($student, 'CS202', 1.75, '1st Semester', '2025-2026', true);
        $this->attachCompletedCourse($student, 'MATH201', 2.00, '1st Semester', '2025-2026', true);
        
        // CURRENTLY ENROLLED - Year 2, 2nd Semester (2025-2026) - CURRENT SEMESTER
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'status' => 'approved',
            'total_units' => 12,
            'submitted_at' => now()->subDays(10),
            'reviewed_at' => now()->subDays(8),
        ]);
        
        // Add courses to current enrollment
        $this->attachEnrollmentCourse($enrollment, 'CS301', 'Monday/Wednesday', '09:00', '10:30', 'CS-301', 'Prof. Garcia');
        $this->attachEnrollmentCourse($enrollment, 'CS302', 'Tuesday/Thursday', '09:00', '10:30', 'CS-302', 'Prof. Reyes');
        $this->attachEnrollmentCourse($enrollment, 'PHYS201', 'Monday/Wednesday', '13:00', '14:30', 'SCI-201', 'Prof. Cruz');
        $this->attachEnrollmentCourse($enrollment, 'ECON101', 'Friday', '10:00', '13:00', 'BUS-101', 'Prof. Tan');
        
        $this->command->info('Created regular 2nd year student: Maria Santos (2024-REG-001)');
        $this->command->info('  - Passed: 12 courses from Year 1 and first semester of Year 2');
        $this->command->info('  - Currently Enrolled: 4 courses (CS301, CS302, PHYS201, ECON101)');
        $this->command->info('  - Future: Year 3 and Year 4 courses');
    }
    
    /**
     * Create a regular 3rd year student with all previous courses passed
     * Currently enrolled in 3rd year 2nd semester courses
     */
    private function createRegularStudent3rdYear(School $school): void
    {
        $student = Student::create([
            'student_id' => '2024-REG-002',
            'email' => 'regular.student2@student.edu',
            'password' => Hash::make('password'),
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'school_id' => $school->id,
            'year_level' => 3,
            'status' => 'active',
        ]);
        
        Payment::create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'amount' => 5000.00,
            'status' => 'paid',
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'paid_at' => now(),
        ]);
        
        // All Year 1 courses - PASSED
        $this->attachCompletedCourse($student, 'CS101', 1.50, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'MATH101', 1.25, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'ENGL101', 1.75, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'PE101', 1.00, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'HIST101', 1.50, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'PHYS101', 1.50, '2nd Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'CHEM101', 1.75, '2nd Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'ENGL201', 1.25, '2nd Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'STAT101', 1.50, '2nd Semester', '2023-2024', true);
        
        // All Year 2 courses - PASSED
        $this->attachCompletedCourse($student, 'CS201', 1.25, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'CS202', 1.50, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'MATH201', 1.75, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'CS301', 1.50, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'CS302', 1.75, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'PHYS201', 2.00, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'ECON101', 1.75, '2nd Semester', '2024-2025', true);
        
        // Year 3, 1st Semester - PASSED
        $this->attachCompletedCourse($student, 'BUS101', 1.50, '1st Semester', '2025-2026', true);
        $this->attachCompletedCourse($student, 'ACCT101', 1.75, '1st Semester', '2025-2026', true);
        
        // CURRENTLY ENROLLED - Year 3, 2nd Semester
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'status' => 'approved',
            'total_units' => 15,
            'submitted_at' => now()->subDays(12),
            'reviewed_at' => now()->subDays(9),
        ]);
        
        $this->attachEnrollmentCourse($enrollment, 'BUS201', 'Monday/Wednesday', '10:30', '12:00', 'BUS-201', 'Prof. Lim');
        $this->attachEnrollmentCourse($enrollment, 'BUS202', 'Tuesday/Thursday', '10:30', '12:00', 'BUS-202', 'Prof. Wong');
        $this->attachEnrollmentCourse($enrollment, 'ECON201', 'Friday', '09:00', '12:00', 'BUS-301', 'Prof. Santos');
        
        $this->command->info('Created regular 3rd year student: Juan Dela Cruz (2024-REG-002)');
        $this->command->info('  - Passed: 18 courses from Year 1, Year 2, and first semester of Year 3');
        $this->command->info('  - Currently Enrolled: 3 courses (BUS201, BUS202, ECON201)');
        $this->command->info('  - Future: Remaining Year 3 and all Year 4 courses');
    }

    
    /**
     * Create an irregular 2nd year student with some failed courses
     * Has to retake failed courses alongside current year courses
     */
    private function createIrregularStudent2ndYear(School $school): void
    {
        $student = Student::create([
            'student_id' => '2024-IRR-001',
            'email' => 'irregular.student1@student.edu',
            'password' => Hash::make('password'),
            'first_name' => 'Pedro',
            'last_name' => 'Gonzales',
            'school_id' => $school->id,
            'year_level' => 2,
            'status' => 'active',
        ]);
        
        Payment::create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'amount' => 5000.00,
            'status' => 'paid',
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'paid_at' => now(),
        ]);
        
        // Year 1, 1st Semester - Mixed results
        $this->attachCompletedCourse($student, 'CS101', 5.00, '1st Semester', '2024-2025', false); // FAILED
        $this->attachCompletedCourse($student, 'MATH101', 1.75, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'ENGL101', 2.00, '1st Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'PE101', 5.00, '1st Semester', '2024-2025', false); // FAILED
        $this->attachCompletedCourse($student, 'HIST101', 2.25, '1st Semester', '2024-2025', true);
        
        // Year 1, 2nd Semester - Mixed results
        $this->attachCompletedCourse($student, 'PHYS101', 2.00, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'CHEM101', 5.00, '2nd Semester', '2024-2025', false); // FAILED
        $this->attachCompletedCourse($student, 'ENGL201', 1.75, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'STAT101', 2.25, '2nd Semester', '2024-2025', true);
        
        // Year 2, 1st Semester - Retook CS101 and passed, but failed MATH201
        $this->attachCompletedCourse($student, 'CS101', 2.00, '1st Semester', '2025-2026', true); // RETAKE - PASSED
        $this->attachCompletedCourse($student, 'CS202', 2.25, '1st Semester', '2025-2026', true);
        $this->attachCompletedCourse($student, 'MATH201', 5.00, '1st Semester', '2025-2026', false); // FAILED
        
        // CURRENTLY ENROLLED - Year 2, 2nd Semester
        // Retaking failed courses + some new courses
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'status' => 'submitted',
            'total_units' => 14,
            'submitted_at' => now()->subDays(5),
        ]);
        
        // Retaking failed courses
        $this->attachEnrollmentCourse($enrollment, 'PE101', 'Monday', '14:00', '16:00', 'GYM-1', 'Prof. Rivera'); // RETAKE
        $this->attachEnrollmentCourse($enrollment, 'CHEM101', 'Tuesday/Thursday', '13:00', '14:30', 'SCI-101', 'Prof. Aquino'); // RETAKE
        $this->attachEnrollmentCourse($enrollment, 'MATH201', 'Monday/Wednesday', '15:00', '16:30', 'MATH-201', 'Prof. Bautista'); // RETAKE
        
        // New courses (can't take CS201 yet because CS101 prerequisite was just passed last sem)
        $this->attachEnrollmentCourse($enrollment, 'ECON101', 'Friday', '10:00', '13:00', 'BUS-101', 'Prof. Tan');
        
        $this->command->info('Created irregular 2nd year student: Pedro Gonzales (2024-IRR-001)');
        $this->command->info('  - Passed: 8 courses');
        $this->command->info('  - Failed: 4 courses (CS101-retaken and passed, PE101, CHEM101, MATH201)');
        $this->command->info('  - Currently Enrolled: 4 courses (3 retakes + 1 new)');
        $this->command->info('  - Status: Irregular due to failed courses');
    }
    
    /**
     * Create an irregular 3rd year student with multiple failed courses
     * Behind schedule due to failures and retakes
     */
    private function createIrregularStudent3rdYear(School $school): void
    {
        $student = Student::create([
            'student_id' => '2024-IRR-002',
            'email' => 'irregular.student2@student.edu',
            'password' => Hash::make('password'),
            'first_name' => 'Ana',
            'last_name' => 'Mercado',
            'school_id' => $school->id,
            'year_level' => 3,
            'status' => 'active',
        ]);
        
        Payment::create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'amount' => 5000.00,
            'status' => 'paid',
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'paid_at' => now(),
        ]);
        
        // Year 1 - Multiple failures
        $this->attachCompletedCourse($student, 'CS101', 5.00, '1st Semester', '2023-2024', false); // FAILED
        $this->attachCompletedCourse($student, 'MATH101', 2.00, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'ENGL101', 2.25, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'PE101', 1.75, '1st Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'HIST101', 5.00, '1st Semester', '2023-2024', false); // FAILED
        
        $this->attachCompletedCourse($student, 'PHYS101', 5.00, '2nd Semester', '2023-2024', false); // FAILED
        $this->attachCompletedCourse($student, 'CHEM101', 2.25, '2nd Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'ENGL201', 2.00, '2nd Semester', '2023-2024', true);
        $this->attachCompletedCourse($student, 'STAT101', 1.75, '2nd Semester', '2023-2024', true);
        
        // Year 2 - Retakes and new courses, more failures
        $this->attachCompletedCourse($student, 'CS101', 2.50, '1st Semester', '2024-2025', true); // RETAKE - PASSED
        $this->attachCompletedCourse($student, 'HIST101', 2.00, '1st Semester', '2024-2025', true); // RETAKE - PASSED
        $this->attachCompletedCourse($student, 'CS202', 5.00, '1st Semester', '2024-2025', false); // FAILED
        $this->attachCompletedCourse($student, 'MATH201', 2.25, '1st Semester', '2024-2025', true);
        
        $this->attachCompletedCourse($student, 'PHYS101', 2.00, '2nd Semester', '2024-2025', true); // RETAKE - PASSED
        $this->attachCompletedCourse($student, 'CS201', 2.50, '2nd Semester', '2024-2025', true);
        $this->attachCompletedCourse($student, 'ECON101', 2.00, '2nd Semester', '2024-2025', true);
        
        // Year 3, 1st Semester - Retook CS202 and passed
        $this->attachCompletedCourse($student, 'CS202', 2.25, '1st Semester', '2025-2026', true); // RETAKE - PASSED
        $this->attachCompletedCourse($student, 'PHYS201', 2.50, '1st Semester', '2025-2026', true);
        $this->attachCompletedCourse($student, 'BUS101', 5.00, '1st Semester', '2025-2026', false); // FAILED
        
        // CURRENTLY ENROLLED - Year 3, 2nd Semester
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'status' => 'submitted',
            'total_units' => 15,
            'submitted_at' => now()->subDays(3),
        ]);
        
        // Mix of retakes and progression
        $this->attachEnrollmentCourse($enrollment, 'BUS101', 'Monday/Wednesday', '08:00', '09:30', 'BUS-101', 'Prof. Ramos'); // RETAKE
        $this->attachEnrollmentCourse($enrollment, 'CS301', 'Tuesday/Thursday', '09:00', '10:30', 'CS-301', 'Prof. Garcia'); // NEW (now has prereqs)
        $this->attachEnrollmentCourse($enrollment, 'CS302', 'Monday/Wednesday', '13:00', '14:30', 'CS-302', 'Prof. Reyes'); // NEW
        $this->attachEnrollmentCourse($enrollment, 'ACCT101', 'Friday', '14:00', '17:00', 'BUS-201', 'Prof. Villanueva'); // NEW
        
        $this->command->info('Created irregular 3rd year student: Ana Mercado (2024-IRR-002)');
        $this->command->info('  - Passed: 15 courses (including retakes)');
        $this->command->info('  - Failed: 5 courses initially (CS101, HIST101, PHYS101, CS202, BUS101)');
        $this->command->info('  - Retaken and Passed: 4 courses (CS101, HIST101, PHYS101, CS202)');
        $this->command->info('  - Currently Enrolled: 4 courses (1 retake + 3 new)');
        $this->command->info('  - Status: Irregular, behind schedule due to multiple failures and retakes');
    }

    
    /**
     * Helper method to attach a completed course to a student
     */
    private function attachCompletedCourse(
        Student $student, 
        string $courseCode, 
        float $grade, 
        string $semester, 
        string $academicYear, 
        bool $passed
    ): void {
        $course = Course::where('course_code', $courseCode)->first();
        
        if (!$course) {
            $this->command->warn("Course {$courseCode} not found, skipping...");
            return;
        }
        
        // Convert numeric grade to letter grade for display
        $letterGrade = $this->numericToLetterGrade($grade);
        
        // Check if this course is already attached (for retakes)
        $existing = DB::table('student_completed_courses')
            ->where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();
        
        if ($existing) {
            // Update the existing record (retake scenario)
            DB::table('student_completed_courses')
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->update([
                    'grade' => $letterGrade,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                    'passed' => $passed,
                    'updated_at' => now(),
                ]);
        } else {
            // Insert new record
            $student->completedCourses()->attach($course->id, [
                'grade' => $letterGrade,
                'semester' => $semester,
                'academic_year' => $academicYear,
                'passed' => $passed,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    /**
     * Helper method to attach a course to an enrollment
     */
    private function attachEnrollmentCourse(
        Enrollment $enrollment,
        string $courseCode,
        string $scheduleDay,
        string $startTime,
        string $endTime,
        string $room,
        string $instructor
    ): void {
        $course = Course::where('course_code', $courseCode)->first();
        
        if (!$course) {
            $this->command->warn("Course {$courseCode} not found for enrollment, skipping...");
            return;
        }
        
        DB::table('enrollment_courses')->insert([
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'schedule_day' => $scheduleDay,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room' => $room,
            'instructor' => $instructor,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Convert numeric grade to letter grade
     */
    private function numericToLetterGrade(float $grade): string
    {
        if ($grade >= 5.00) return 'F';
        if ($grade >= 3.00) return 'D';
        if ($grade >= 2.50) return 'C';
        if ($grade >= 2.00) return 'B+';
        if ($grade >= 1.75) return 'B';
        if ($grade >= 1.50) return 'A-';
        if ($grade >= 1.25) return 'A';
        return 'A+';
    }
}
