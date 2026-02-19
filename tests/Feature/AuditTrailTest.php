<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Professor;
use App\Models\Enrollment;
use App\Models\EnrollmentAuditLog;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function it_creates_audit_log_on_schedule_submission()
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
        ]);

        // Simulate submission
        $enrollment->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        EnrollmentAuditLog::logAction(
            enrollment: $enrollment,
            action: 'submitted',
            userId: $student->id,
            userType: 'student',
            oldStatus: 'draft',
            newStatus: 'submitted'
        );

        $this->assertDatabaseHas('enrollment_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'action' => 'submitted',
            'user_type' => 'student',
            'old_status' => 'draft',
            'new_status' => 'submitted',
        ]);
    }

    /** @test */
    public function it_creates_audit_log_on_schedule_approval()
    {
        $school = School::first();
        $professor = Professor::factory()->create(['school_id' => $school->id]);
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);

        // Simulate approval
        $enrollment->update([
            'status' => 'approved',
            'professor_id' => $professor->id,
            'reviewed_at' => now(),
        ]);

        EnrollmentAuditLog::logAction(
            enrollment: $enrollment,
            action: 'approve',
            userId: $professor->id,
            userType: 'professor',
            oldStatus: 'submitted',
            newStatus: 'approved',
            comments: 'Schedule looks good'
        );

        $this->assertDatabaseHas('enrollment_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'action' => 'approve',
            'user_type' => 'professor',
            'old_status' => 'submitted',
            'new_status' => 'approved',
        ]);
    }

    /** @test */
    public function it_creates_audit_log_on_schedule_rejection()
    {
        $school = School::first();
        $professor = Professor::factory()->create(['school_id' => $school->id]);
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);

        // Simulate rejection
        $enrollment->update([
            'status' => 'rejected',
            'professor_id' => $professor->id,
            'reviewed_at' => now(),
        ]);

        EnrollmentAuditLog::logAction(
            enrollment: $enrollment,
            action: 'reject',
            userId: $professor->id,
            userType: 'professor',
            oldStatus: 'submitted',
            newStatus: 'draft',
            comments: 'Please fix schedule conflicts'
        );

        $this->assertDatabaseHas('enrollment_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'action' => 'reject',
            'user_type' => 'professor',
            'old_status' => 'submitted',
            'new_status' => 'draft',
        ]);
    }

    /** @test */
    public function it_retrieves_enrollment_audit_history()
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
        ]);

        // Create multiple audit log entries
        EnrollmentAuditLog::logAction(
            enrollment: $enrollment,
            action: 'submitted',
            userId: $student->id,
            userType: 'student',
            oldStatus: 'draft',
            newStatus: 'submitted'
        );

        EnrollmentAuditLog::logAction(
            enrollment: $enrollment,
            action: 'approve',
            userId: 1,
            userType: 'professor',
            oldStatus: 'submitted',
            newStatus: 'approved'
        );

        $history = EnrollmentAuditLog::getEnrollmentHistory($enrollment);

        $this->assertCount(2, $history);
        $this->assertEquals('approve', $history->first()->action);
        $this->assertEquals('submitted', $history->last()->action);
    }

    /** @test */
    public function enrollment_has_audit_logs_relationship()
    {
        $student = Student::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
        ]);

        EnrollmentAuditLog::logAction(
            enrollment: $enrollment,
            action: 'submitted',
            userId: $student->id,
            userType: 'student',
            oldStatus: 'draft',
            newStatus: 'submitted'
        );

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $enrollment->auditLogs);
        $this->assertCount(1, $enrollment->auditLogs);
    }
}
