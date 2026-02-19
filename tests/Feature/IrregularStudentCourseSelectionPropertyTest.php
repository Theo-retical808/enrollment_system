<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Professor;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Property 1: Authentication and Access Control
 * Validates: Requirements 1.1, 1.2, 1.3
 */
class IrregularStudentCourseSelectionPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected IrregularStudentEnrollmentService $enrollmentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollmentService = app(IrregularStudentEnrollmentService::class);
    }

    /**
     * Property: Irregular students can access course selection interface
     * Validates: Requirement 5.1
     *
     * @test
     */
    public function irregular_students_can_access_course_selection_interface()
    {
        $school = School::factory()->create(['code' => 'CS', 'name' => 'Computer Science']);
        $student = Student::factory()->create(['school_id' => $school->id]);

        $failedCourse = Course::factory()->create([
            'course_code' => 'CS101',
            'title' => 'Intro to Programming',
            'units' => 3,
            'school_id' => $school->id,
            'is_active' => true
        ]);

        $student->completedCourses()->attach($failedCourse->id, [
            'grade' => 5.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => false
        ]);

        $this->assertTrue($student->isIrregular());
        $enrollment = $this->enrollmentService->createManualEnrollment($student);

        $this->assertNotNull($enrollment);
        $this->assertEquals('draft', $enrollment->status);
        $this->assertEquals(0, $enrollment->total_units);
    }
}

