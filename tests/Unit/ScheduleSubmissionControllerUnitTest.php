<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ScheduleSubmissionController;
use App\Services\ScheduleValidationService;
use App\Models\Student;
use App\Models\Professor;
use App\Models\School;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ScheduleSubmissionControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new ScheduleValidationService();
        $this->controller = new ScheduleSubmissionController($this->validationService);
    }

    /** @test */
    public function it_instantiates_controller_with_validation_service()
    {
        $this->assertInstanceOf(ScheduleSubmissionController::class, $this->controller);
    }

    /** @test */
    public function validation_service_can_validate_empty_schedule()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $courses = collect([]);
        
        $result = $this->validationService->validateSchedule($student, $courses);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('is_valid', $result);
        $this->assertArrayHasKey('unit_load', $result);
        $this->assertEquals(0, $result['unit_load']);
    }

    /** @test */
    public function validation_service_calculates_unit_load_correctly()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $courses = Course::factory()->count(3)->create(['units' => 3]);
        
        $result = $this->validationService->validateSchedule($student, $courses);
        
        $this->assertEquals(9, $result['unit_load']); // 3 courses × 3 units
        $this->assertTrue($result['is_valid']); // Should be valid as it's under 21 units
    }

    /** @test */
    public function validation_service_detects_unit_overload()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $courses = Course::factory()->count(8)->create(['units' => 3]);
        
        $result = $this->validationService->validateSchedule($student, $courses);
        
        $this->assertEquals(24, $result['unit_load']); // 8 courses × 3 units = 24
        $this->assertFalse($result['is_valid']); // Should be invalid as it exceeds 21 units
        $this->assertTrue($result['unit_limit_exceeded']);
    }
}