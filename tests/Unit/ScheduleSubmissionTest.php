<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Course;
use App\Models\School;
use App\Models\Enrollment;
use App\Services\ScheduleValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_validate_schedule_before_submission()
    {
        // Create test data
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $courses = Course::factory()->count(3)->create(['school_id' => $school->id, 'units' => 3]);
        
        $validationService = new ScheduleValidationService();
        $result = $validationService->validateSchedule($student, $courses);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('is_valid', $result);
        $this->assertArrayHasKey('unit_load', $result);
        $this->assertEquals(9, $result['unit_load']); // 3 courses × 3 units
    }

    /** @test */
    public function it_detects_unit_load_exceeding_limit()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $courses = Course::factory()->count(8)->create(['school_id' => $school->id, 'units' => 3]);
        
        $validationService = new ScheduleValidationService();
        $result = $validationService->validateSchedule($student, $courses);
        
        $this->assertFalse($result['is_valid']);
        $this->assertTrue($result['unit_limit_exceeded']);
        $this->assertEquals(24, $result['unit_load']); // 8 courses × 3 units = 24 > 21
    }
}