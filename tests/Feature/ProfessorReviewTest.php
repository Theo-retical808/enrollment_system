<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Professor;
use App\Models\Student;
use App\Models\School;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ProfessorReviewTest extends TestCase
{
    use RefreshDatabase;

    protected $professor;
    protected $student;
    protected $school;
    protected $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a school
        $this->school = School::factory()->create();

        // Create a professor
        $this->professor = Professor::factory()->create([
            'school_id' => $this->school->id,
            'status' => 'active',
        ]);

        // Create a student
        $this->student = Student::factory()->create([
            'school_id' => $this->school->id,
            'status' => 'active',
        ]);

        // Create an enrollment
        $this->enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Add some courses to the enrollment
        $courses = Course::factory()->count(3)->create([
            'school_id' => $this->school->id,
        ]);

        foreach ($courses as $course) {
            $this->enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '09:00:00',
                'room' => 'Room 101',
                'instructor' => 'Test Instructor',
            ]);
        }
    }

    /** @test */
    public function professor_can_view_dashboard()
    {
        $response = $this->actingAs($this->professor, 'professor')
            ->get(route('professor.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('professor.dashboard');
        $response->assertViewHas('pendingEnrollments');
    }

    /** @test */
    public function professor_can_view_schedule_review_page()
    {
        $response = $this->actingAs($this->professor, 'professor')
            ->get(route('professor.review', $this->enrollment->id));

        $response->assertStatus(200);
        $response->assertViewIs('professor.review-schedule');
        $response->assertViewHas('enrollment');
    }

    /** @test */
    public function professor_can_approve_schedule()
    {
        $response = $this->actingAs($this->professor, 'professor')
            ->post(route('professor.approve', $this->enrollment->id), [
                'action' => 'approve',
                'review_comments' => 'Schedule looks good!',
            ]);

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHas('success');

        $this->enrollment->refresh();
        $this->assertEquals('approved', $this->enrollment->status);
        $this->assertEquals('Schedule looks good!', $this->enrollment->review_comments);
        $this->assertEquals($this->professor->id, $this->enrollment->professor_id);
        $this->assertNotNull($this->enrollment->reviewed_at);
    }

    /** @test */
    public function professor_can_reject_schedule()
    {
        $response = $this->actingAs($this->professor, 'professor')
            ->post(route('professor.approve', $this->enrollment->id), [
                'action' => 'reject',
                'review_comments' => 'Please revise your schedule.',
            ]);

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHas('success');

        $this->enrollment->refresh();
        $this->assertEquals('rejected', $this->enrollment->status);
        $this->assertEquals('Please revise your schedule.', $this->enrollment->review_comments);
        $this->assertEquals($this->professor->id, $this->enrollment->professor_id);
        $this->assertNotNull($this->enrollment->reviewed_at);
    }

    /** @test */
    public function professor_cannot_review_enrollment_from_different_school()
    {
        // Create a different school and student
        $otherSchool = School::factory()->create();
        $otherStudent = Student::factory()->create([
            'school_id' => $otherSchool->id,
        ]);

        $otherEnrollment = Enrollment::factory()->create([
            'student_id' => $otherStudent->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->professor, 'professor')
            ->get(route('professor.review', $otherEnrollment->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function professor_cannot_review_non_submitted_enrollment()
    {
        $this->enrollment->update(['status' => 'draft']);

        $response = $this->actingAs($this->professor, 'professor')
            ->get(route('professor.review', $this->enrollment->id));

        $response->assertRedirect(route('professor.dashboard'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_professor_dashboard()
    {
        $response = $this->get(route('professor.dashboard'));

        $response->assertRedirect(route('professor.login'));
    }
}
