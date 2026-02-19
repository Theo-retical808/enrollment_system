<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function approved_schedule_can_be_exported_as_pdf()
    {
        $student = Student::factory()->create();
        $school = School::first();
        $student->school_id = $school->id;
        $student->save();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $courses = Course::factory()->count(3)->create(['school_id' => $school->id]);
        foreach ($courses as $course) {
            $enrollment->courses()->attach($course->id, [
                'schedule_day' => 'Monday',
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'room' => 'Room 101',
                'instructor' => 'Prof. Test',
            ]);
        }

        $response = $this->actingAs($student, 'student')
            ->get(route('student.schedule.export.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->assertStringContainsString($student->student_id, $response->getContent());
        $this->assertStringContainsString('Official Student Schedule', $response->getContent());
    }

    /** @test */
    public function approved_schedule_can_be_exported_as_csv()
    {
        $student = Student::factory()->create();
        $school = School::first();
        $student->school_id = $school->id;
        $student->save();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $course = Course::factory()->create([
            'school_id' => $school->id,
            'course_code' => 'CS101',
            'title' => 'Introduction to Computer Science',
        ]);

        $enrollment->courses()->attach($course->id, [
            'schedule_day' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'room' => 'Room 101',
            'instructor' => 'Prof. Test',
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.schedule.export.csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->getContent();
        $this->assertStringContainsString('Student Enrollment Schedule', $content);
        $this->assertStringContainsString($student->student_id, $content);
        $this->assertStringContainsString('CS101', $content);
        $this->assertStringContainsString('Introduction to Computer Science', $content);
    }

    /** @test */
    public function non_approved_schedule_cannot_be_exported_as_pdf()
    {
        $student = Student::factory()->create();
        $school = School::first();
        $student->school_id = $school->id;
        $student->save();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.schedule.export.pdf'));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error', 'Only approved schedules can be exported.');
    }

    /** @test */
    public function schedule_can_be_emailed()
    {
        $student = Student::factory()->create();
        $school = School::first();
        $student->school_id = $school->id;
        $student->save();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($student, 'student')
            ->post(route('student.schedule.email'));

        $response->assertRedirect(route('student.schedule'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function csv_export_includes_all_course_details()
    {
        $student = Student::factory()->create();
        $school = School::first();
        $student->school_id = $school->id;
        $student->save();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'approved',
            'semester' => 'First Semester',
            'academic_year' => '2025-2026',
        ]);

        $courses = Course::factory()->count(2)->create(['school_id' => $school->id]);
        foreach ($courses as $index => $course) {
            $enrollment->courses()->attach($course->id, [
                'schedule_day' => $index === 0 ? 'Monday' : 'Wednesday',
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'room' => 'Room ' . ($index + 101),
                'instructor' => 'Prof. Instructor ' . ($index + 1),
            ]);
        }

        $response = $this->actingAs($student, 'student')
            ->get(route('student.schedule.export.csv'));

        $content = $response->getContent();
        
        // Check header information
        $this->assertStringContainsString($student->full_name, $content);
        $this->assertStringContainsString('First Semester', $content);
        $this->assertStringContainsString('2025-2026', $content);
        
        // Check course details
        foreach ($courses as $course) {
            $this->assertStringContainsString($course->course_code, $content);
            $this->assertStringContainsString($course->title, $content);
        }
        
        // Check schedule details
        $this->assertStringContainsString('Monday', $content);
        $this->assertStringContainsString('Wednesday', $content);
        $this->assertStringContainsString('Room 101', $content);
        $this->assertStringContainsString('Room 102', $content);
    }

    /** @test */
    public function unauthenticated_user_cannot_export_schedule()
    {
        $response = $this->get(route('student.schedule.export.pdf'));
        $response->assertRedirect(route('student.login'));

        $response = $this->get(route('student.schedule.export.csv'));
        $response->assertRedirect(route('student.login'));
    }
}
