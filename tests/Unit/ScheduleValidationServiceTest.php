<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ScheduleValidationService;
use App\Models\Student;
use App\Models\Course;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class ScheduleValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ScheduleValidationService $validationService;
    protected Student $student;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new ScheduleValidationService();
        
        // Create test school
        $this->school = School::create([
            'name' => 'Test School',
            'code' => 'TEST',
        ]);
        
        // Create test student
        $this->student = Student::create([
            'student_id' => 'TEST001',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'first_name' => 'Test',
            'last_name' => 'Student',
            'school_id' => $this->school->id,
            'year_level' => 2,
        ]);
    }

    public function test_calculate_unit_load()
    {
        $courses = collect([
            (object) ['units' => 3],
            (object) ['units' => 4],
            (object) ['units' => 2],
        ]);

        $totalUnits = $this->validationService->calculateUnitLoad($courses);
        
        $this->assertEquals(9, $totalUnits);
    }

    public function test_unit_load_limit_validation()
    {
        // Create courses that exceed 21 units
        $courses = collect([
            (object) ['units' => 12],
            (object) ['units' => 10],
        ]);

        $validation = $this->validationService->validateSchedule($this->student, $courses);
        
        $this->assertFalse($validation['is_valid']);
        $this->assertTrue($validation['unit_limit_exceeded']);
        $this->assertEquals(22, $validation['unit_load']);
        $this->assertContains('Unit load (22 units) exceeds the maximum limit of 21 units.', $validation['errors']);
    }

    public function test_valid_schedule_within_limits()
    {
        // Create courses within limits
        $courses = collect([
            (object) ['units' => 3],
            (object) ['units' => 4],
            (object) ['units' => 3],
        ]);

        $validation = $this->validationService->validateSchedule($this->student, $courses);
        
        $this->assertTrue($validation['is_valid']);
        $this->assertFalse($validation['unit_limit_exceeded']);
        $this->assertEquals(10, $validation['unit_load']);
        $this->assertEmpty($validation['errors']);
    }

    public function test_schedule_conflict_detection()
    {
        // Create courses with overlapping schedules
        $course1 = (object) [
            'id' => 1,
            'course_code' => 'CS101',
            'title' => 'Intro to CS',
            'units' => 3,
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ]
        ];

        $course2 = (object) [
            'id' => 2,
            'course_code' => 'MATH101',
            'title' => 'Calculus',
            'units' => 4,
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
            ]
        ];

        $courses = collect([$course1, $course2]);
        $validation = $this->validationService->validateSchedule($this->student, $courses);
        
        $this->assertFalse($validation['is_valid']);
        $this->assertNotEmpty($validation['schedule_conflicts']);
        $this->assertCount(1, $validation['schedule_conflicts']);
    }

    public function test_no_schedule_conflict_different_days()
    {
        // Create courses on different days
        $course1 = (object) [
            'id' => 1,
            'course_code' => 'CS101',
            'title' => 'Intro to CS',
            'units' => 3,
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ]
        ];

        $course2 = (object) [
            'id' => 2,
            'course_code' => 'MATH101',
            'title' => 'Calculus',
            'units' => 4,
            'pivot' => (object) [
                'schedule_day' => 'Tuesday',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ]
        ];

        $courses = collect([$course1, $course2]);
        $validation = $this->validationService->validateSchedule($this->student, $courses);
        
        $this->assertTrue($validation['is_valid']);
        $this->assertEmpty($validation['schedule_conflicts']);
    }

    public function test_time_overlap_detection()
    {
        // Test the hasTimeOverlap method indirectly through schedule validation
        $this->assertTrue($this->validationService->hasTimeOverlap('08:00:00', '09:30:00', '09:00:00', '10:30:00'));
        $this->assertFalse($this->validationService->hasTimeOverlap('08:00:00', '09:00:00', '09:00:00', '10:00:00'));
        $this->assertFalse($this->validationService->hasTimeOverlap('08:00:00', '09:00:00', '10:00:00', '11:00:00'));
    }

    public function test_prerequisite_validation_with_satisfied_prerequisites()
    {
        // Create prerequisite course
        $prerequisite = Course::create([
            'course_code' => 'MATH101',
            'title' => 'Basic Mathematics',
            'description' => 'Basic math course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create main course with prerequisite
        $mainCourse = Course::create([
            'course_code' => 'MATH201',
            'title' => 'Advanced Mathematics',
            'description' => 'Advanced math course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Set up prerequisite relationship
        $mainCourse->prerequisites()->attach($prerequisite->id);

        // Add completed course to student (with passing grade)
        $this->student->completedCourses()->attach($prerequisite->id, [
            'grade' => 'A',
            'semester' => 'Fall',
            'academic_year' => '2023-2024',
            'passed' => true,
        ]);

        $courses = collect([$mainCourse]);
        $violations = $this->validationService->validatePrerequisites($this->student, $courses);

        $this->assertEmpty($violations);
    }

    public function test_prerequisite_validation_with_missing_prerequisites()
    {
        // Create prerequisite course
        $prerequisite = Course::create([
            'course_code' => 'MATH101',
            'title' => 'Basic Mathematics',
            'description' => 'Basic math course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create main course with prerequisite
        $mainCourse = Course::create([
            'course_code' => 'MATH201',
            'title' => 'Advanced Mathematics',
            'description' => 'Advanced math course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Set up prerequisite relationship
        $mainCourse->prerequisites()->attach($prerequisite->id);

        // Don't add completed course to student (missing prerequisite)
        $courses = collect([$mainCourse]);
        $violations = $this->validationService->validatePrerequisites($this->student, $courses);

        $this->assertNotEmpty($violations);
        $this->assertCount(1, $violations);
        $this->assertEquals($mainCourse->id, $violations[0]['course_id']);
        $this->assertEquals($prerequisite->id, $violations[0]['prerequisite_id']);
        $this->assertStringContainsString('Cannot enroll in MATH201', $violations[0]['message']);
        $this->assertStringContainsString('missing prerequisite: MATH101', $violations[0]['message']);
    }

    public function test_prerequisite_validation_with_failed_prerequisite()
    {
        // Create prerequisite course
        $prerequisite = Course::create([
            'course_code' => 'MATH101',
            'title' => 'Basic Mathematics',
            'description' => 'Basic math course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create main course with prerequisite
        $mainCourse = Course::create([
            'course_code' => 'MATH201',
            'title' => 'Advanced Mathematics',
            'description' => 'Advanced math course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Set up prerequisite relationship
        $mainCourse->prerequisites()->attach($prerequisite->id);

        // Add completed course to student with failing grade
        $this->student->completedCourses()->attach($prerequisite->id, [
            'grade' => 'F',
            'semester' => 'Fall',
            'academic_year' => '2023-2024',
            'passed' => false,
        ]);

        $courses = collect([$mainCourse]);
        $violations = $this->validationService->validatePrerequisites($this->student, $courses);

        $this->assertNotEmpty($violations);
        $this->assertCount(1, $violations);
        $this->assertEquals($mainCourse->id, $violations[0]['course_id']);
        $this->assertEquals($prerequisite->id, $violations[0]['prerequisite_id']);
    }

    public function test_comprehensive_schedule_validation()
    {
        // Create courses that violate multiple constraints
        $course1 = (object) [
            'id' => 1,
            'course_code' => 'CS101',
            'title' => 'Intro to CS',
            'units' => 12, // High units
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ]
        ];

        $course2 = (object) [
            'id' => 2,
            'course_code' => 'MATH101',
            'title' => 'Calculus',
            'units' => 10, // High units - total will exceed 21
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '09:00:00', // Conflicts with course1
                'end_time' => '10:30:00',
            ]
        ];

        $courses = collect([$course1, $course2]);
        $validation = $this->validationService->validateSchedule($this->student, $courses);

        $this->assertFalse($validation['is_valid']);
        $this->assertTrue($validation['unit_limit_exceeded']);
        $this->assertEquals(22, $validation['unit_load']);
        $this->assertNotEmpty($validation['schedule_conflicts']);
        $this->assertNotEmpty($validation['errors']);
        
        // Should have both unit limit and schedule conflict errors
        $errorMessages = implode(' ', $validation['errors']);
        $this->assertStringContainsString('exceeds the maximum limit', $errorMessages);
        $this->assertStringContainsString('Schedule conflict', $errorMessages);
    }

    public function test_course_addition_validation()
    {
        // Create existing courses
        $existingCourse = (object) [
            'id' => 1,
            'course_code' => 'CS101',
            'title' => 'Intro to CS',
            'units' => 15,
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ]
        ];

        $existingCourses = collect([$existingCourse]);

        // Create new course to add
        $newCourse = (object) [
            'id' => 2,
            'course_code' => 'MATH101',
            'title' => 'Calculus',
            'units' => 7, // Would exceed 21 unit limit
            'pivot' => (object) [
                'schedule_day' => 'Tuesday',
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
            ]
        ];

        $validation = $this->validationService->validateCourseAddition($this->student, $newCourse, $existingCourses);

        $this->assertFalse($validation['is_valid']);
        $this->assertTrue($validation['unit_limit_exceeded']);
        $this->assertEquals(22, $validation['unit_load']);
    }

    public function test_can_add_course_method()
    {
        // Create existing courses within limit
        $existingCourse = (object) [
            'id' => 1,
            'course_code' => 'CS101',
            'title' => 'Intro to CS',
            'units' => 12,
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ]
        ];

        $existingCourses = collect([$existingCourse]);

        // Create new course that can be added
        $newCourse = (object) [
            'id' => 2,
            'course_code' => 'MATH101',
            'title' => 'Calculus',
            'units' => 6, // Within limit
            'pivot' => (object) [
                'schedule_day' => 'Tuesday',
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
            ]
        ];

        $result = $this->validationService->canAddCourse($this->student, $newCourse, $existingCourses);

        $this->assertTrue($result['can_add']);
        $this->assertEquals(18, $result['new_unit_load']);
        $this->assertFalse($result['would_exceed_limit']);
        $this->assertEmpty($result['reasons']);
    }

    public function test_edge_cases_with_invalid_data()
    {
        // Test with courses that have missing or invalid data
        $courses = collect([
            (object) ['units' => 3], // Missing other properties
            (object) ['id' => 1, 'course_code' => 'CS101', 'units' => 'invalid'], // Invalid units
            (object) ['id' => 2, 'course_code' => 'MATH101', 'units' => -5], // Negative units
            (object) ['id' => 3, 'course_code' => 'ENGL101', 'units' => 4], // Valid course
        ]);

        $unitLoad = $this->validationService->calculateUnitLoad($courses);
        
        // Should only count the valid course with 4 units
        $this->assertEquals(4, $unitLoad);
    }

    public function test_schedule_validation_with_incomplete_schedule_data()
    {
        // Create courses with incomplete schedule data
        $course1 = (object) [
            'id' => 1,
            'course_code' => 'CS101',
            'title' => 'Intro to CS',
            'units' => 3,
            'pivot' => (object) [
                'schedule_day' => 'Monday',
                // Missing start_time and end_time
            ]
        ];

        $course2 = (object) [
            'id' => 2,
            'course_code' => 'MATH101',
            'title' => 'Calculus',
            'units' => 4,
            // Missing pivot data entirely
        ];

        $courses = collect([$course1, $course2]);
        $validation = $this->validationService->validateSchedule($this->student, $courses);

        // Should still be valid since no conflicts can be detected with incomplete data
        $this->assertTrue($validation['is_valid']);
        $this->assertEquals(7, $validation['unit_load']);
        $this->assertEmpty($validation['schedule_conflicts']);
    }

    public function test_prerequisite_validation_with_courses_without_prerequisites()
    {
        // Create course without prerequisites
        $course = Course::create([
            'course_code' => 'ENGL101',
            'title' => 'English Composition',
            'description' => 'Basic English course',
            'units' => 3,
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $courses = collect([$course]);
        $violations = $this->validationService->validatePrerequisites($this->student, $courses);

        // Should have no violations since course has no prerequisites
        $this->assertEmpty($violations);
    }
}