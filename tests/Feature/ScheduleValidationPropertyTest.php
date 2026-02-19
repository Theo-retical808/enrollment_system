<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\School;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\ScheduleValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Property 6: Comprehensive Schedule Validation
 * Validates: Requirements 7.1, 7.2, 7.3, 8.1, 8.2, 9.1, 9.2
 */
class ScheduleValidationPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new ScheduleValidationService();
    }

    /**
     * Property: Total units never exceed 21
     * 
     * @test
     */
    public function total_units_never_exceed_21()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        // Create courses totaling 24 units
        $courses = Course::factory()->count(8)->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        foreach ($courses as $course) {
            $enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:00:00'
            ]);
        }

        $enrollment->refresh();
        $validation = $this->validationService->validateSchedule($enrollment);

        $this->assertFalse($validation['unit_load_valid']);
        $this->assertGreaterThan(21, $enrollment->total_units);
    }

    /**
     * Property: Courses with time conflicts are detected
     * 
     * @test
     */
    public function time_conflicts_are_detected()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $course1 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        $course2 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        // Both courses on Monday 8-10 AM (conflict)
        $enrollment->courses()->attach($course1->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $enrollment->courses()->attach($course2->id, [
            'schedule_day' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00'
        ]);

        $validation = $this->validationService->validateSchedule($enrollment);

        $this->assertFalse($validation['no_conflicts']);
        $this->assertNotEmpty($validation['conflicts']);
    }

    /**
     * Property: Non-overlapping courses have no conflicts
     * 
     * @test
     */
    public function non_overlapping_courses_have_no_conflicts()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $course1 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        $course2 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        // Different times, no conflict
        $enrollment->courses()->attach($course1->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $enrollment->courses()->attach($course2->id, [
            'schedule_day' => 'Monday',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00'
        ]);

        $validation = $this->validationService->validateSchedule($enrollment);

        $this->assertTrue($validation['no_conflicts']);
    }

    /**
     * Property: Different days never conflict
     * 
     * @test
     */
    public function different_days_never_conflict()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $course1 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        $course2 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        // Same time, different days
        $enrollment->courses()->attach($course1->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $enrollment->courses()->attach($course2->id, [
            'schedule_day' => 'Tuesday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $validation = $this->validationService->validateSchedule($enrollment);

        $this->assertTrue($validation['no_conflicts']);
    }

    /**
     * Property: Unit load calculation is accurate
     * 
     * @test
     */
    public function unit_load_calculation_is_accurate()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $expectedUnits = 0;
        $courseCounts = [3, 3, 4, 3, 2]; // Total: 15 units

        foreach ($courseCounts as $index => $units) {
            $course = Course::factory()->create([
                'school_id' => $school->id,
                'units' => $units
            ]);

            $enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Monday',
                'start_time' => sprintf('%02d:00:00', 8 + $index * 2),
                'end_time' => sprintf('%02d:00:00', 10 + $index * 2)
            ]);

            $expectedUnits += $units;
        }

        $enrollment->refresh();

        $this->assertEquals($expectedUnits, $enrollment->total_units);
        $this->assertEquals(15, $enrollment->total_units);
    }

    /**
     * Property: Empty schedule is valid but has zero units
     * 
     * @test
     */
    public function empty_schedule_is_valid_with_zero_units()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $validation = $this->validationService->validateSchedule($enrollment);

        $this->assertTrue($validation['unit_load_valid']);
        $this->assertTrue($validation['no_conflicts']);
        $this->assertEquals(0, $enrollment->total_units);
    }

    /**
     * Property: Validation is idempotent (same result on repeated calls)
     * 
     * @test
     */
    public function validation_is_idempotent()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $course = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        $enrollment->courses()->attach($course->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $validation1 = $this->validationService->validateSchedule($enrollment);
        $validation2 = $this->validationService->validateSchedule($enrollment);
        $validation3 = $this->validationService->validateSchedule($enrollment);

        $this->assertEquals($validation1, $validation2);
        $this->assertEquals($validation2, $validation3);
    }

    /**
     * Property: Adding valid course maintains validity
     * 
     * @test
     */
    public function adding_valid_course_maintains_validity()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'pending'
        ]);

        $course1 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        $enrollment->courses()->attach($course1->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $validation1 = $this->validationService->validateSchedule($enrollment);
        $this->assertTrue($validation1['unit_load_valid']);
        $this->assertTrue($validation1['no_conflicts']);

        // Add another non-conflicting course
        $course2 = Course::factory()->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        $enrollment->courses()->attach($course2->id, [
            'schedule_day' => 'Tuesday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00'
        ]);

        $validation2 = $this->validationService->validateSchedule($enrollment);
        $this->assertTrue($validation2['unit_load_valid']);
        $this->assertTrue($validation2['no_conflicts']);
    }
}
