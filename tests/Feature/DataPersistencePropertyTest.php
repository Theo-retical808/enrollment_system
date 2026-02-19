<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Professor;
use App\Models\School;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentAuditLog;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Property 13: Data Persistence and Audit Trail
 * Validates: Requirements 14.1, 14.2, 14.4
 */
class DataPersistencePropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property: All enrollment transactions are persisted with timestamps
     * 
     * **Validates: Requirements 14.1**
     * 
     * @test
     */
    public function enrollment_transactions_persisted_with_timestamps()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $beforeCreate = Carbon::now()->subSecond();
        
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'semester' => '1st Semester',
            'academic_year' => '2024-2025',
            'status' => 'draft',
            'total_units' => 0,
        ]);
        
        $afterCreate = Carbon::now()->addSecond();
        
        // Verify persistence
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'student_id' => $student->id,
            'semester' => '1st Semester',
            'academic_year' => '2024-2025',
        ]);
        
        // Verify timestamps
        $this->assertNotNull($enrollment->created_at);
        $this->assertNotNull($enrollment->updated_at);
        $this->assertTrue($enrollment->created_at->between($beforeCreate, $afterCreate));
        $this->assertTrue($enrollment->updated_at->between($beforeCreate, $afterCreate));
    }

    /**
     * Property: Enrollment updates maintain proper timestamps
     * 
     * **Validates: Requirements 14.1**
     * 
     * @test
     */
    public function enrollment_updates_maintain_timestamps()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
        ]);
        
        $originalCreatedAt = $enrollment->created_at;
        $originalUpdatedAt = $enrollment->updated_at;
        
        // Wait a moment to ensure timestamp difference
        sleep(1);
        
        $beforeUpdate = Carbon::now()->subSecond();
        $enrollment->update(['status' => 'submitted', 'submitted_at' => now()]);
        $afterUpdate = Carbon::now()->addSecond();
        
        $enrollment->refresh();
        
        // created_at should not change
        $this->assertEquals($originalCreatedAt->timestamp, $enrollment->created_at->timestamp);
        
        // updated_at should change
        $this->assertNotEquals($originalUpdatedAt->timestamp, $enrollment->updated_at->timestamp);
        $this->assertTrue($enrollment->updated_at->between($beforeUpdate, $afterUpdate));
        
        // submitted_at should be set
        $this->assertNotNull($enrollment->submitted_at);
    }

    /**
     * Property: Referential integrity maintained between students and enrollments
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function referential_integrity_student_enrollments()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
        ]);
        
        // Verify relationship
        $this->assertEquals($student->id, $enrollment->student->id);
        $this->assertTrue($student->enrollments->contains($enrollment));
        
        // Verify database constraint
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'student_id' => $student->id,
        ]);
    }

    /**
     * Property: Referential integrity maintained between enrollments and courses
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function referential_integrity_enrollment_courses()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        
        $course = Course::factory()->create(['school_id' => $school->id]);
        
        $enrollment->courses()->attach($course->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'room' => 'Room 101',
            'instructor' => 'Dr. Smith',
        ]);
        
        // Verify relationship
        $this->assertTrue($enrollment->courses->contains($course));
        $this->assertTrue($course->enrollments->contains($enrollment));
        
        // Verify pivot data persisted
        $this->assertDatabaseHas('enrollment_courses', [
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);
    }

    /**
     * Property: Cascade deletion maintains referential integrity
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function cascade_deletion_maintains_integrity()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        
        $course = Course::factory()->create(['school_id' => $school->id]);
        $enrollment->courses()->attach($course->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);
        
        $enrollmentId = $enrollment->id;
        
        // Delete enrollment
        $enrollment->delete();
        
        // Verify enrollment deleted
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollmentId]);
        
        // Verify pivot records deleted (cascade)
        $this->assertDatabaseMissing('enrollment_courses', [
            'enrollment_id' => $enrollmentId,
        ]);
    }

    /**
     * Property: Audit logs created for enrollment submissions
     * 
     * **Validates: Requirements 14.4**
     * 
     * @test
     */
    public function audit_logs_created_for_submissions()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
        ]);
        
        // Log submission action
        $auditLog = EnrollmentAuditLog::logAction(
            $enrollment,
            'submitted',
            $student->id,
            'student',
            'draft',
            'submitted',
            'Student submitted schedule for review'
        );
        
        // Verify audit log persisted
        $this->assertDatabaseHas('enrollment_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'user_id' => $student->id,
            'user_type' => 'student',
            'action' => 'submitted',
            'old_status' => 'draft',
            'new_status' => 'submitted',
        ]);
        
        // Verify timestamp
        $this->assertNotNull($auditLog->action_timestamp);
    }

    /**
     * Property: Audit logs created for professor approvals
     * 
     * **Validates: Requirements 14.4**
     * 
     * @test
     */
    public function audit_logs_created_for_approvals()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $professor = Professor::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'professor_id' => $professor->id,
            'status' => 'submitted',
        ]);
        
        // Log approval action
        $auditLog = EnrollmentAuditLog::logAction(
            $enrollment,
            'approved',
            $professor->id,
            'professor',
            'submitted',
            'approved',
            'Schedule approved by professor',
            ['approved_by' => $professor->full_name]
        );
        
        // Verify audit log persisted
        $this->assertDatabaseHas('enrollment_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'user_id' => $professor->id,
            'user_type' => 'professor',
            'action' => 'approved',
            'old_status' => 'submitted',
            'new_status' => 'approved',
        ]);
        
        // Verify metadata
        $this->assertNotNull($auditLog->metadata);
        $this->assertArrayHasKey('approved_by', $auditLog->metadata);
    }

    /**
     * Property: Audit logs created for rejections with comments
     * 
     * **Validates: Requirements 14.4**
     * 
     * @test
     */
    public function audit_logs_created_for_rejections()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $professor = Professor::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'professor_id' => $professor->id,
            'status' => 'submitted',
        ]);
        
        $rejectionComment = 'Unit load exceeds recommended limit';
        
        // Log rejection action
        $auditLog = EnrollmentAuditLog::logAction(
            $enrollment,
            'rejected',
            $professor->id,
            'professor',
            'submitted',
            'rejected',
            $rejectionComment
        );
        
        // Verify audit log persisted with comments
        $this->assertDatabaseHas('enrollment_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'action' => 'rejected',
            'comments' => $rejectionComment,
        ]);
        
        $this->assertEquals($rejectionComment, $auditLog->comments);
    }

    /**
     * Property: Audit trail maintains chronological order
     * 
     * **Validates: Requirements 14.4**
     * 
     * @test
     */
    public function audit_trail_maintains_chronological_order()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $professor = Professor::factory()->create(['school_id' => $school->id]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'status' => 'draft',
        ]);
        
        // Create multiple audit logs
        $log1 = EnrollmentAuditLog::logAction($enrollment, 'created', $student->id, 'student', null, 'draft');
        sleep(1);
        
        $log2 = EnrollmentAuditLog::logAction($enrollment, 'modified', $student->id, 'student', 'draft', 'draft');
        sleep(1);
        
        $log3 = EnrollmentAuditLog::logAction($enrollment, 'submitted', $student->id, 'student', 'draft', 'submitted');
        sleep(1);
        
        $log4 = EnrollmentAuditLog::logAction($enrollment, 'approved', $professor->id, 'professor', 'submitted', 'approved');
        
        // Retrieve audit history
        $history = EnrollmentAuditLog::getEnrollmentHistory($enrollment);
        
        // Verify chronological order (most recent first)
        $this->assertEquals(4, $history->count());
        $this->assertEquals('approved', $history[0]->action);
        $this->assertEquals('submitted', $history[1]->action);
        $this->assertEquals('modified', $history[2]->action);
        $this->assertEquals('created', $history[3]->action);
        
        // Verify timestamps are in order
        $this->assertTrue($log1->action_timestamp->lt($log2->action_timestamp));
        $this->assertTrue($log2->action_timestamp->lt($log3->action_timestamp));
        $this->assertTrue($log3->action_timestamp->lt($log4->action_timestamp));
    }

    /**
     * Property: Course prerequisites maintain referential integrity
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function course_prerequisites_maintain_integrity()
    {
        $school = School::factory()->create();
        
        $prerequisiteCourse = Course::factory()->create([
            'school_id' => $school->id,
            'course_code' => 'CS101',
        ]);
        
        $advancedCourse = Course::factory()->create([
            'school_id' => $school->id,
            'course_code' => 'CS201',
        ]);
        
        // Attach prerequisite
        $advancedCourse->prerequisites()->attach($prerequisiteCourse->id);
        
        // Verify relationship
        $this->assertTrue($advancedCourse->prerequisites->contains($prerequisiteCourse));
        $this->assertTrue($prerequisiteCourse->dependentCourses->contains($advancedCourse));
        
        // Verify database
        $this->assertDatabaseHas('course_prerequisites', [
            'course_id' => $advancedCourse->id,
            'prerequisite_id' => $prerequisiteCourse->id,
        ]);
    }

    /**
     * Property: Student completed courses maintain referential integrity
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function student_completed_courses_maintain_integrity()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $course = Course::factory()->create(['school_id' => $school->id]);
        
        // Attach completed course
        $student->completedCourses()->attach($course->id, [
            'grade' => 2.0,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
            'passed' => true,
        ]);
        
        // Verify relationship
        $this->assertTrue($student->completedCourses->contains($course));
        $this->assertTrue($course->completedByStudents->contains($student));
        
        // Verify pivot data
        $this->assertDatabaseHas('student_completed_courses', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'grade' => 2.0,
            'passed' => true,
        ]);
    }

    /**
     * Property: Payment records maintain referential integrity with students
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function payment_records_maintain_integrity()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'amount' => 5000.00,
            'status' => 'paid',
        ]);
        
        // Verify relationship
        $this->assertEquals($student->id, $payment->student->id);
        $this->assertTrue($student->payments->contains($payment));
        
        // Verify database
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'student_id' => $student->id,
        ]);
    }

    /**
     * Property: Audit logs can be queried by action type
     * 
     * **Validates: Requirements 14.4**
     * 
     * @test
     */
    public function audit_logs_queryable_by_action()
    {
        $school = School::factory()->create();
        $student1 = Student::factory()->create(['school_id' => $school->id]);
        $student2 = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment1 = Enrollment::factory()->create(['student_id' => $student1->id]);
        $enrollment2 = Enrollment::factory()->create(['student_id' => $student2->id]);
        
        // Create various audit logs
        EnrollmentAuditLog::logAction($enrollment1, 'submitted', $student1->id, 'student');
        EnrollmentAuditLog::logAction($enrollment2, 'submitted', $student2->id, 'student');
        EnrollmentAuditLog::logAction($enrollment1, 'approved', null, 'professor');
        
        // Query by action
        $submittedLogs = EnrollmentAuditLog::getActionLogs('submitted');
        $approvedLogs = EnrollmentAuditLog::getActionLogs('approved');
        
        $this->assertEquals(2, $submittedLogs->count());
        $this->assertEquals(1, $approvedLogs->count());
    }

    /**
     * Property: Pivot table timestamps are maintained
     * 
     * **Validates: Requirements 14.1**
     * 
     * @test
     */
    public function pivot_table_timestamps_maintained()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        $course = Course::factory()->create(['school_id' => $school->id]);
        
        $beforeAttach = Carbon::now()->subSecond();
        
        $enrollment->courses()->attach($course->id, [
            'schedule_day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
        ]);
        
        $afterAttach = Carbon::now()->addSecond();
        
        // Retrieve pivot data
        $pivotData = $enrollment->courses()->where('course_id', $course->id)->first()->pivot;
        
        // Verify timestamps exist
        $this->assertNotNull($pivotData->created_at);
        $this->assertNotNull($pivotData->updated_at);
        $this->assertTrue($pivotData->created_at->between($beforeAttach, $afterAttach));
    }

    /**
     * Property: Multiple enrollments per student maintain integrity
     * 
     * **Validates: Requirements 14.2**
     * 
     * @test
     */
    public function multiple_enrollments_per_student_maintain_integrity()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        
        $enrollment1 = Enrollment::factory()->create([
            'student_id' => $student->id,
            'semester' => '1st Semester',
            'academic_year' => '2023-2024',
        ]);
        
        $enrollment2 = Enrollment::factory()->create([
            'student_id' => $student->id,
            'semester' => '2nd Semester',
            'academic_year' => '2023-2024',
        ]);
        
        $enrollment3 = Enrollment::factory()->create([
            'student_id' => $student->id,
            'semester' => '1st Semester',
            'academic_year' => '2024-2025',
        ]);
        
        // Verify all enrollments linked to student
        $this->assertEquals(3, $student->enrollments()->count());
        $this->assertTrue($student->enrollments->contains($enrollment1));
        $this->assertTrue($student->enrollments->contains($enrollment2));
        $this->assertTrue($student->enrollments->contains($enrollment3));
    }

    /**
     * Property: Audit metadata is properly serialized and deserialized
     * 
     * **Validates: Requirements 14.4**
     * 
     * @test
     */
    public function audit_metadata_properly_serialized()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        
        $metadata = [
            'courses_added' => ['CS101', 'CS102'],
            'total_units' => 6,
            'validation_passed' => true,
            'ip_address' => '192.168.1.1',
        ];
        
        $auditLog = EnrollmentAuditLog::logAction(
            $enrollment,
            'modified',
            $student->id,
            'student',
            'draft',
            'draft',
            'Added courses to schedule',
            $metadata
        );
        
        // Verify metadata stored
        $this->assertNotNull($auditLog->metadata);
        $this->assertIsArray($auditLog->metadata);
        $this->assertEquals($metadata, $auditLog->metadata);
        
        // Verify retrieval from database
        $retrieved = EnrollmentAuditLog::find($auditLog->id);
        $this->assertEquals($metadata, $retrieved->metadata);
    }
}
