<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\School;
use App\Models\Payment;
use App\Services\PaymentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Property 2: Payment-Based Access Control
 * 
 * **Validates: Requirements 2.1, 2.2, 2.3, 2.4**
 * 
 * For any student accessing the enrollment system, if their enrollment fee is paid,
 * they should see schedule options and be able to proceed with enrollment, while
 * unpaid students should see payment prompts and have schedule options hidden.
 */
class PaymentVerificationPropertyTest extends TestCase
{
    use RefreshDatabase;

    private PaymentVerificationService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentVerificationService();
    }

    /**
     * Property: Students with paid enrollment fee can access enrollment features
     * Validates: Requirements 2.1, 2.2
     * 
     * @test
     */
    public function students_with_paid_enrollment_fee_can_access_enrollment()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        // Create paid enrollment fee for current semester
        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        // Test regular enrollment access
        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));

        $response->assertStatus(200);
        $response->assertViewIs('student.regular-enrollment');
    }

    /**
     * Property: Students without payment record cannot access enrollment
     * Validates: Requirements 2.1, 2.3
     * 
     * @test
     */
    public function students_without_payment_record_cannot_access_enrollment()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        // No payment record exists
        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));

        $response->assertRedirect(route('student.payment.required'));
        $response->assertSessionHas('payment_status');
        
        $paymentStatus = session('payment_status');
        $this->assertEquals('not_found', $paymentStatus['status']);
        $this->assertFalse($paymentStatus['can_enroll']);
        $this->assertTrue($paymentStatus['payment_required']);
    }

    /**
     * Property: Students with pending payment cannot access enrollment
     * Validates: Requirements 2.1, 2.3
     * 
     * @test
     */
    public function students_with_pending_payment_cannot_access_enrollment()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'pending',
            'amount' => 5000.00
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));

        $response->assertRedirect(route('student.payment.required'));
        
        $paymentStatus = session('payment_status');
        $this->assertEquals('pending', $paymentStatus['status']);
        $this->assertFalse($paymentStatus['can_enroll']);
    }

    /**
     * Property: Students with failed payment cannot access enrollment
     * Validates: Requirements 2.1, 2.3
     * 
     * @test
     */
    public function students_with_failed_payment_cannot_access_enrollment()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'failed',
            'amount' => 5000.00
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));

        $response->assertRedirect(route('student.payment.required'));
        
        $paymentStatus = session('payment_status');
        $this->assertEquals('failed', $paymentStatus['status']);
        $this->assertFalse($paymentStatus['can_enroll']);
    }

    /**
     * Property: Payment verification checks current semester only
     * Validates: Requirements 2.1, 2.4
     * 
     * @test
     */
    public function payment_verification_checks_current_semester_only()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        // Create paid payment for previous semester
        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => '2nd Semester',
            'academic_year' => '2023-2024',
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()->subMonths(6)
        ]);

        // Should not allow access with old semester payment
        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));

        $response->assertRedirect(route('student.payment.required'));
    }

    /**
     * Property: Payment status changes update access permissions immediately
     * Validates: Requirements 2.4
     * 
     * @test
     */
    public function payment_status_changes_update_access_immediately()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'pending',
            'amount' => 5000.00
        ]);

        // Initially cannot access
        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));
        $response->assertRedirect(route('student.payment.required'));

        // Mark payment as paid
        $payment->markAsPaid();

        // Now can access immediately
        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));
        $response->assertStatus(200);
    }

    /**
     * Property: Payment verification works for both regular and irregular students
     * Validates: Requirements 2.1, 2.2
     * 
     * @test
     */
    public function payment_verification_works_for_both_student_types()
    {
        $school = School::factory()->create();
        
        // Regular student with payment
        $regularStudent = Student::factory()->create(['school_id' => $school->id]);
        Payment::factory()->create([
            'student_id' => $regularStudent->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        // Irregular student with payment
        $irregularStudent = Student::factory()->create(['school_id' => $school->id]);
        $irregularStudent->completedCourses()->attach(
            \App\Models\Course::factory()->create(['school_id' => $school->id]),
            ['passed' => false, 'grade' => 5.0, 'semester' => '1st Semester', 'academic_year' => '2023-2024']
        );
        Payment::factory()->create([
            'student_id' => $irregularStudent->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        // Both should be able to access their respective enrollment routes
        $regularResponse = $this->actingAs($regularStudent, 'student')
            ->get(route('student.enrollment.regular'));
        $regularResponse->assertStatus(200);

        $irregularResponse = $this->actingAs($irregularStudent, 'student')
            ->get(route('student.enrollment.irregular'));
        $irregularResponse->assertStatus(200);
    }

    /**
     * Property: Only enrollment_fee payment type grants enrollment access
     * Validates: Requirements 2.1
     * 
     * @test
     */
    public function only_enrollment_fee_payment_grants_access()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        // Create paid tuition payment (not enrollment fee)
        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'tuition',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 15000.00,
            'paid_at' => now()
        ]);

        // Should not grant access
        $response = $this->actingAs($student, 'student')
            ->get(route('student.enrollment.regular'));
        $response->assertRedirect(route('student.payment.required'));
    }

    /**
     * Property: Payment service correctly identifies payment status
     * Validates: Requirements 2.1
     * 
     * @test
     */
    public function payment_service_correctly_identifies_payment_status()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        // Test no payment
        $this->assertFalse($this->paymentService->isEnrollmentFeePaid($student));

        // Test pending payment
        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'pending',
            'amount' => 5000.00
        ]);
        $this->assertFalse($this->paymentService->isEnrollmentFeePaid($student));

        // Test paid payment
        $payment->markAsPaid();
        $this->assertTrue($this->paymentService->isEnrollmentFeePaid($student));

        // Test failed payment
        $payment->markAsFailed();
        $this->assertFalse($this->paymentService->isEnrollmentFeePaid($student));
    }

    /**
     * Property: Payment status provides complete information
     * Validates: Requirements 2.2, 2.3
     * 
     * @test
     */
    public function payment_status_provides_complete_information()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        $status = $this->paymentService->getPaymentStatus($student);

        $this->assertArrayHasKey('status', $status);
        $this->assertArrayHasKey('message', $status);
        $this->assertArrayHasKey('can_enroll', $status);
        $this->assertArrayHasKey('payment_required', $status);
        
        $this->assertEquals('paid', $status['status']);
        $this->assertTrue($status['can_enroll']);
        $this->assertFalse($status['payment_required']);
        $this->assertArrayHasKey('paid_at', $status);
        $this->assertArrayHasKey('amount_paid', $status);
    }

    /**
     * Property: Dashboard displays payment status correctly
     * Validates: Requirements 2.2, 2.3
     * 
     * @test
     */
    public function dashboard_displays_payment_status_correctly()
    {
        $school = School::factory()->create();
        
        // Student with paid enrollment fee
        $paidStudent = Student::factory()->create(['school_id' => $school->id]);
        Payment::factory()->create([
            'student_id' => $paidStudent->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        $response = $this->actingAs($paidStudent, 'student')
            ->get(route('student.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewHas('canAccessEnrollment');
        $canAccess = $response->viewData('canAccessEnrollment');
        $this->assertTrue($canAccess['can_access']);

        // Student without payment
        $unpaidStudent = Student::factory()->create(['school_id' => $school->id]);
        
        $response = $this->actingAs($unpaidStudent, 'student')
            ->get(route('student.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewHas('canAccessEnrollment');
        $canAccess = $response->viewData('canAccessEnrollment');
        $this->assertFalse($canAccess['can_access']);
    }

    /**
     * Property: Multiple students can have independent payment statuses
     * Validates: Requirements 2.1, 2.4
     * 
     * @test
     */
    public function multiple_students_have_independent_payment_statuses()
    {
        $school = School::factory()->create();
        
        $student1 = Student::factory()->create(['school_id' => $school->id]);
        $student2 = Student::factory()->create(['school_id' => $school->id]);
        $student3 = Student::factory()->create(['school_id' => $school->id]);

        // Student 1: Paid
        Payment::factory()->create([
            'student_id' => $student1->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        // Student 2: Pending
        Payment::factory()->create([
            'student_id' => $student2->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'pending',
            'amount' => 5000.00
        ]);

        // Student 3: No payment

        // Verify independent statuses
        $this->assertTrue($this->paymentService->isEnrollmentFeePaid($student1));
        $this->assertFalse($this->paymentService->isEnrollmentFeePaid($student2));
        $this->assertFalse($this->paymentService->isEnrollmentFeePaid($student3));
    }

    /**
     * Property: Payment verification is consistent across multiple checks
     * Validates: Requirements 2.1, 2.4
     * 
     * @test
     */
    public function payment_verification_is_consistent()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create(['school_id' => $school->id]);

        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type' => 'enrollment_fee',
            'semester' => $this->paymentService->getCurrentSemester(),
            'academic_year' => $this->paymentService->getCurrentAcademicYear(),
            'status' => 'paid',
            'amount' => 5000.00,
            'paid_at' => now()
        ]);

        // Multiple checks should return consistent results
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue($this->paymentService->isEnrollmentFeePaid($student));
            
            $status = $this->paymentService->getPaymentStatus($student);
            $this->assertEquals('paid', $status['status']);
            $this->assertTrue($status['can_enroll']);
            
            $accessInfo = $this->paymentService->canAccessEnrollment($student);
            $this->assertTrue($accessInfo['can_access']);
        }
    }
}
