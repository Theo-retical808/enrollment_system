<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\School;
use App\Models\Course;
use App\Services\StudentClassificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Property 3: Student Classification and Workflow Routing
 * Validates: Requirements 3.1, 3.2, 3.4
 */
class StudentClassificationPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected $classificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classificationService = new StudentClassificationService();
    }

    /**
     * Property: Students with no failed courses are regular
     * 
     * @test
     */
    public function students_with_no_failed_courses_are_regular()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        // Add only passed courses
        $courses = Course::factory()->count(5)->create([
            'school_id' => $school->id
        ]);

        foreach ($courses as $course) {
            $student->completedCourses()->attach($course->id, [
                'grade' => 2.0, // Passing grade
                'semester' => '1st Semester',
                'academic_year' => '2023-2024',
                'passed' => true
            ]);
        }

        $this->assertTrue($student->isRegular());
        $this->assertFalse($student->isIrregular());
    }

    /**
     * Property: Students with any failed course are irregular
     * 
     * @test
     */
    public function students_with_failed_courses_are_irregular()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $passedCourse = Course::factory()->create([
            'school_id' => $school->id
        ]);

        $failedCourse = Course::factory()->create([
            'school_id' => $school->id
        ]);

        $student->completedCourses()->attach($passedCourse->id, [
            'grade' => 2.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => true
        ]);

        $student->completedCourses()->attach($failedCourse->id, [
            'grade' => 5.0, // Failing grade
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => false
        ]);

        $this->assertFalse($student->isRegular());
        $this->assertTrue($student->isIrregular());
    }

    /**
     * Property: New students with no history are regular
     * 
     * @test
     */
    public function new_students_with_no_history_are_regular()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $this->assertTrue($student->isRegular());
        $this->assertFalse($student->isIrregular());
    }

    /**
     * Property: Classification is consistent across multiple checks
     * 
     * @test
     */
    public function classification_is_consistent()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $course = Course::factory()->create([
            'school_id' => $school->id
        ]);

        $student->completedCourses()->attach($course->id, [
            'grade' => 5.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => false
        ]);

        // Check multiple times
        $result1 = $student->isIrregular();
        $result2 = $student->isIrregular();
        $result3 = $student->isIrregular();

        $this->assertEquals($result1, $result2);
        $this->assertEquals($result2, $result3);
        $this->assertTrue($result1);
    }

    /**
     * Property: Regular students are routed to regular enrollment
     * 
     * @test
     */
    public function regular_students_routed_to_regular_enrollment()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        // Check that the dashboard shows Regular status
        $response->assertSee('Regular');
        // Check that the classification message indicates automatic schedule assignment
        $response->assertSee('You will be automatically assigned a schedule based on your program');
    }

    /**
     * Property: Irregular students are routed to irregular enrollment
     * 
     * @test
     */
    public function irregular_students_routed_to_irregular_enrollment()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $course = Course::factory()->create([
            'school_id' => $school->id
        ]);

        $student->completedCourses()->attach($course->id, [
            'grade' => 5.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => false
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        // Check that the dashboard shows Irregular status
        $response->assertSee('Irregular');
        // Check that the classification message indicates course selection
        $response->assertSee('You can select your own courses');
    }

    /**
     * Property: Grade threshold correctly identifies failures
     * 
     * @test
     */
    public function grade_threshold_correctly_identifies_failures()
    {
        $school = School::factory()->create();
        
        // Test boundary cases
        $passingGrades = [1.0, 1.5, 2.0, 2.5, 3.0];
        $failingGrades = [5.0, 4.0];

        foreach ($passingGrades as $grade) {
            $student = Student::factory()->create([
                'school_id' => $school->id
            ]);

            $course = Course::factory()->create([
                'school_id' => $school->id
            ]);

            $student->completedCourses()->attach($course->id, [
                'grade' => $grade,
                'semester' => '1st Semester',
                'academic_year' => '2023-2024',
                'passed' => true
            ]);

            $this->assertTrue($student->isRegular(), "Grade {$grade} should be passing");
        }

        foreach ($failingGrades as $grade) {
            $student = Student::factory()->create([
                'school_id' => $school->id
            ]);

            $course = Course::factory()->create([
                'school_id' => $school->id
            ]);

            $student->completedCourses()->attach($course->id, [
                'grade' => $grade,
                'semester' => '1st Semester',
                'academic_year' => '2023-2024',
                'passed' => false
            ]);

            $this->assertTrue($student->isIrregular(), "Grade {$grade} should be failing");
        }
    }

    /**
     * Property: Multiple failed courses still result in irregular status
     * 
     * @test
     */
    public function multiple_failed_courses_result_in_irregular()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $failedCourses = Course::factory()->count(3)->create([
            'school_id' => $school->id
        ]);

        foreach ($failedCourses as $course) {
            $student->completedCourses()->attach($course->id, [
                'grade' => 5.0,
                'semester' => '1st Semester',
                'academic_year' => '2023-2024',
                'passed' => false
            ]);
        }

        $this->assertTrue($student->isIrregular());
        $this->assertEquals(3, $student->completedCourses()->wherePivot('passed', false)->count());
    }

    /**
     * Property: Classification persists across sessions
     * 
     * @test
     */
    public function classification_persists_across_sessions()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $course = Course::factory()->create([
            'school_id' => $school->id
        ]);

        $student->completedCourses()->attach($course->id, [
            'grade' => 5.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => false
        ]);

        // First check
        $this->assertTrue($student->isIrregular());

        // Refresh from database
        $student->refresh();

        // Second check after refresh
        $this->assertTrue($student->isIrregular());
    }

    /**
     * Property: Classification based on passed field is accurate
     * Tests that the system correctly uses the 'passed' pivot field
     * 
     * @test
     */
    public function classification_uses_passed_field_correctly()
    {
        $school = School::factory()->create();
        
        // Test student with passed=true courses
        $regularStudent = Student::factory()->create([
            'school_id' => $school->id
        ]);
        
        $courses = Course::factory()->count(3)->create([
            'school_id' => $school->id
        ]);
        
        foreach ($courses as $course) {
            $regularStudent->completedCourses()->attach($course->id, [
                'grade' => 2.0,
                'semester' => '1st Semester',
                'academic_year' => '2023-2024',
                'passed' => true
            ]);
        }
        
        $this->assertTrue($regularStudent->isRegular());
        $this->assertFalse($regularStudent->hasFailedCourses());
        
        // Test student with passed=false course
        $irregularStudent = Student::factory()->create([
            'school_id' => $school->id
        ]);
        
        $failedCourse = Course::factory()->create([
            'school_id' => $school->id
        ]);
        
        $irregularStudent->completedCourses()->attach($failedCourse->id, [
            'grade' => 5.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => false
        ]);
        
        $this->assertTrue($irregularStudent->isIrregular());
        $this->assertTrue($irregularStudent->hasFailedCourses());
    }

    /**
     * Property: Random course history classification
     * Simulates property-based testing by testing multiple random scenarios
     * 
     * @test
     */
    public function random_course_history_classification()
    {
        $school = School::factory()->create();
        
        // Test 20 random scenarios
        for ($i = 0; $i < 20; $i++) {
            $student = Student::factory()->create([
                'school_id' => $school->id
            ]);
            
            $numCourses = rand(1, 10);
            $hasFailure = (bool) rand(0, 1);
            
            $courses = Course::factory()->count($numCourses)->create([
                'school_id' => $school->id
            ]);
            
            foreach ($courses as $index => $course) {
                // Randomly assign one failed course if hasFailure is true
                $isPassed = !($hasFailure && $index === 0);
                $grade = $isPassed ? rand(10, 30) / 10 : 5.0;
                
                $student->completedCourses()->attach($course->id, [
                    'grade' => $grade,
                    'semester' => '1st Semester',
                    'academic_year' => '2023-2024',
                    'passed' => $isPassed
                ]);
            }
            
            // Verify classification matches expectation
            if ($hasFailure) {
                $this->assertTrue($student->isIrregular(), "Student with failed course should be irregular");
                $this->assertFalse($student->isRegular(), "Student with failed course should not be regular");
            } else {
                $this->assertTrue($student->isRegular(), "Student with no failed courses should be regular");
                $this->assertFalse($student->isIrregular(), "Student with no failed courses should not be irregular");
            }
        }
    }

    /**
     * Property: Mixed course history with varying grades
     * Tests classification with realistic grade distributions
     * 
     * @test
     */
    public function mixed_course_history_classification()
    {
        $school = School::factory()->create();
        
        // Test various combinations
        $scenarios = [
            ['passed' => 5, 'failed' => 0, 'expected' => 'regular'],
            ['passed' => 10, 'failed' => 1, 'expected' => 'irregular'],
            ['passed' => 0, 'failed' => 3, 'expected' => 'irregular'],
            ['passed' => 8, 'failed' => 2, 'expected' => 'irregular'],
            ['passed' => 15, 'failed' => 0, 'expected' => 'regular'],
        ];
        
        foreach ($scenarios as $scenario) {
            $student = Student::factory()->create([
                'school_id' => $school->id
            ]);
            
            // Add passed courses
            for ($i = 0; $i < $scenario['passed']; $i++) {
                $course = Course::factory()->create([
                    'school_id' => $school->id
                ]);
                
                $student->completedCourses()->attach($course->id, [
                    'grade' => rand(10, 30) / 10, // 1.0 to 3.0
                    'semester' => '1st Semester',
                    'academic_year' => '2023-2024',
                    'passed' => true
                ]);
            }
            
            // Add failed courses
            for ($i = 0; $i < $scenario['failed']; $i++) {
                $course = Course::factory()->create([
                    'school_id' => $school->id
                ]);
                
                $student->completedCourses()->attach($course->id, [
                    'grade' => 5.0,
                    'semester' => '1st Semester',
                    'academic_year' => '2023-2024',
                    'passed' => false
                ]);
            }
            
            if ($scenario['expected'] === 'regular') {
                $this->assertTrue($student->isRegular(), 
                    "Student with {$scenario['passed']} passed and {$scenario['failed']} failed should be regular");
            } else {
                $this->assertTrue($student->isIrregular(), 
                    "Student with {$scenario['passed']} passed and {$scenario['failed']} failed should be irregular");
            }
        }
    }
}
