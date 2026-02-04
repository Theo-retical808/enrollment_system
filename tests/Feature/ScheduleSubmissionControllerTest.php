<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Course;
use App\Models\School;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\ScheduleValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ScheduleSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $professor;
    protected $school;
    protected $courses;
    protected $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->school = School::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CS'
        ]);

        $this->student = Student::factory()->create([
            'school_id' => $this->school->id,
            'year_level' => 2
        ]);

        $this->professor = Professor::factory()->create([
            'school_id' => $this->school->id,
            'status' => 'active'
        ]);

        // Create courses
        $this->courses = Course::factory()->count(3)->create([
            'school_id' => $this->school->id,
            'units' => 3
        ]);

        // Create enrollment
        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'draft',
            'semester' => 'Fall',
            'academic_year' => '2024-2025'
        ]);

        // Attach courses to enrollment with schedule data
        foreach ($this->courses as $index => $course) {
            $this->enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Monday',
                'start_time' => sprintf('%02d:00', 8 + $index * 2),
                'end_time' => sprintf('%02d:30', 9 + $index * 2),
                'room' => "Room {$index}01",
                'instructor' => "Instructor {$index}"
            ]);
        }

        // Create payment
        Payment::factory()->create([
            'student_id' => $this->student->id,
            'payment_type' => 'enrollment_fee',
            'status' => 'paid',
            'semester' => 'Fall',
            'academic_year' => '2024-2025'
        ]);
    }

    /** @test */
    public function it_can_submit_valid_schedule_for_approval()
    {
        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Submit schedule
        $response = $this->postJson('/student/enrollment/submit');

        // Assert successful submission
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Schedule submitted successfully for professor review.'
            ]);

        // Assert enrollment status updated
        $this->enrollment->refresh();
        $this->assertEquals('submitted', $this->enrollment->status);
        $this->assertNotNull($this->enrollment->professor_id);
        $this->assertNotNull($this->enrollment->submitted_at);
        $this->assertEquals(9, $this->enrollment->total_units); // 3 courses × 3 units
    }

    /** @test */
    public function it_prevents_submission_of_invalid_schedule()
    {
        // Create courses that exceed unit limit
        $extraCourses = Course::factory()->count(5)->create([
            'school_id' => $this->school->id,
            'units' => 4 // This will make total > 21 units
        ]);

        foreach ($extraCourses as $index => $course) {
            $this->enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Tuesday',
                'start_time' => sprintf('%02d:00', 8 + $index),
                'end_time' => sprintf('%02d:30', 9 + $index),
                'room' => "Room {$index}02",
                'instructor' => "Instructor {$index}"
            ]);
        }

        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Attempt to submit invalid schedule
        $response = $this->postJson('/student/enrollment/submit');

        // Assert submission rejected
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Schedule validation failed. Please fix the following issues:'
            ]);

        // Assert enrollment status unchanged
        $this->enrollment->refresh();
        $this->assertEquals('draft', $this->enrollment->status);
    }

    /** @test */
    public function it_assigns_professor_based_on_student_school()
    {
        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Submit schedule
        $response = $this->postJson('/student/enrollment/submit');

        // Assert professor from same school assigned
        $response->assertStatus(200);
        
        $this->enrollment->refresh();
        $this->assertEquals($this->professor->id, $this->enrollment->professor_id);
        $this->assertEquals($this->school->id, $this->enrollment->professor->school_id);
    }

    /** @test */
    public function it_validates_schedule_before_submission()
    {
        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Get validation before submission
        $response = $this->getJson('/student/enrollment/validate');

        // Assert validation response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'can_submit' => true
            ])
            ->assertJsonStructure([
                'validation' => [
                    'is_valid',
                    'unit_load',
                    'errors',
                    'warnings'
                ],
                'summary' => [
                    'total_courses',
                    'total_units',
                    'remaining_units'
                ]
            ]);
    }

    /** @test */
    public function it_checks_submission_eligibility()
    {
        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Check eligibility
        $response = $this->getJson('/student/enrollment/eligibility');

        // Assert eligibility response
        $response->assertStatus(200)
            ->assertJson([
                'eligible' => true,
                'reason' => 'Schedule is valid for submission.'
            ]);
    }

    /** @test */
    public function it_prevents_submission_when_no_courses_selected()
    {
        // Remove all courses from enrollment
        $this->enrollment->courses()->detach();

        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Check eligibility
        $response = $this->getJson('/student/enrollment/eligibility');

        // Assert not eligible
        $response->assertStatus(200)
            ->assertJson([
                'eligible' => false,
                'reason' => 'No courses selected for enrollment.'
            ]);
    }

    /** @test */
    public function it_prevents_resubmission_of_already_submitted_schedule()
    {
        // Mark enrollment as already submitted
        $this->enrollment->update([
            'status' => 'submitted',
            'professor_id' => $this->professor->id,
            'submitted_at' => now()
        ]);

        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Attempt to submit again
        $response = $this->postJson('/student/enrollment/submit');

        // Assert submission rejected
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No draft enrollment found or enrollment already submitted.'
            ]);
    }

    /** @test */
    public function it_gets_submission_status()
    {
        // Update enrollment with submission data
        $this->enrollment->update([
            'status' => 'submitted',
            'professor_id' => $this->professor->id,
            'submitted_at' => now(),
            'total_units' => 9
        ]);

        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Get submission status
        $response = $this->getJson('/student/enrollment/status');

        // Assert status response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'enrollment' => [
                    'status' => 'submitted',
                    'total_units' => 9
                ]
            ])
            ->assertJsonStructure([
                'enrollment' => [
                    'id',
                    'status',
                    'total_units',
                    'submitted_at'
                ],
                'courses',
                'professor' => [
                    'name',
                    'department',
                    'email'
                ]
            ]);
    }

    /** @test */
    public function it_handles_professor_assignment_when_no_school_professors_available()
    {
        // Remove the professor from the same school
        $this->professor->delete();

        // Create professor from different school
        $otherSchool = School::factory()->create(['name' => 'Mathematics']);
        $otherProfessor = Professor::factory()->create([
            'school_id' => $otherSchool->id,
            'status' => 'active'
        ]);

        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Submit schedule
        $response = $this->postJson('/student/enrollment/submit');

        // Assert fallback professor assigned
        $response->assertStatus(200);
        
        $this->enrollment->refresh();
        $this->assertEquals($otherProfessor->id, $this->enrollment->professor_id);
    }

    /** @test */
    public function it_displays_enrolled_courses_on_dashboard()
    {
        // Create test data
        $school = School::factory()->create([
            'name' => 'Computer Science',
            'code' => 'CS'
        ]);

        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 2
        ]);

        // Create courses
        $courses = Course::factory()->count(3)->create([
            'school_id' => $school->id,
            'units' => 3
        ]);

        // Create enrollment
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
            'semester' => 'Fall',
            'academic_year' => '2024-2025'
        ]);

        // Attach courses to enrollment with schedule data
        foreach ($courses as $index => $course) {
            $enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Monday',
                'start_time' => sprintf('%02d:00', 8 + $index * 2),
                'end_time' => sprintf('%02d:30', 9 + $index * 2),
                'room' => "Room {$index}01",
                'instructor' => "Instructor {$index}"
            ]);
        }

        // Create payment
        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'status' => 'paid',
            'semester' => 'Fall',
            'academic_year' => '2024-2025'
        ]);

        // Act as authenticated student
        $this->actingAs($student, 'student');

        // Visit dashboard
        $response = $this->get('/student/dashboard');

        // Assert enrolled courses are displayed
        $response->assertStatus(200);
        
        foreach ($courses as $course) {
            $response->assertSee($course->course_code);
            $response->assertSee($course->title);
            $response->assertSee($course->units);
        }
        
        // Assert schedule information is displayed
        $response->assertSee('Current Schedule (Draft)');
        $response->assertSee('Total Courses: 3');
        $response->assertSee('Total Units: 9');
    }

    /** @test */
    public function it_shows_different_schedule_status_messages()
    {
        // Create test data
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $course = Course::factory()->create(['school_id' => $school->id, 'units' => 3]);

        // Create payment
        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'status' => 'paid',
            'semester' => 'Fall',
            'academic_year' => '2024-2025'
        ]);

        // Test draft status
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft'
        ]);
        $enrollment->courses()->attach($course->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00',
            'end_time' => '09:30',
            'room' => 'Room 101'
        ]);

        $this->actingAs($student, 'student');
        $response = $this->get('/student/dashboard');
        $response->assertSee('Current Schedule (Draft)');

        // Test submitted status
        $enrollment->update(['status' => 'submitted']);
        $response = $this->get('/student/dashboard');
        $response->assertSee('Schedule Under Review');

        // Test approved status
        $enrollment->update(['status' => 'approved']);
        $response = $this->get('/student/dashboard');
        $response->assertSee('Approved Schedule');
    }

    /** @test */
    public function it_assigns_professor_with_lowest_workload()
    {
        // Create additional professor in same school
        $professor2 = Professor::factory()->create([
            'school_id' => $this->school->id,
            'status' => 'active'
        ]);

        // Give first professor higher workload
        Enrollment::factory()->count(3)->create([
            'professor_id' => $this->professor->id,
            'status' => 'submitted'
        ]);

        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Submit schedule
        $response = $this->postJson('/student/enrollment/submit');

        // Assert professor with lower workload assigned
        $response->assertStatus(200);
        
        $this->enrollment->refresh();
        $this->assertEquals($professor2->id, $this->enrollment->professor_id);
    }

    /** @test */
    public function it_displays_schedule_view_page()
    {
        // Act as authenticated student
        $this->actingAs($this->student, 'student');

        // Visit schedule page
        $response = $this->get('/student/schedule');

        // Assert schedule page loads correctly
        $response->assertStatus(200);
        $response->assertSee('Current Schedule (Draft)');
        $response->assertSee($this->enrollment->semester);
        $response->assertSee($this->enrollment->academic_year);
        
        // Assert courses are displayed
        foreach ($this->courses as $course) {
            $response->assertSee($course->course_code);
            $response->assertSee($course->title);
        }
    }
}