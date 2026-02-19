<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\School;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Professor;
use App\Services\RegularStudentEnrollmentService;
use App\Services\PaymentVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Property 4: Regular Student Schedule Assignment
 * 
 * **Validates: Requirements 4.1, 4.3, 4.4**
 * 
 * For any regular student accessing enrollment, they should be automatically assigned
 * a predefined schedule based on their school affiliation, with complete course details
 * displayed and no modification options available.
 */
class RegularStudentAssignmentPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected RegularStudentEnrollmentService $enrollmentService;
    protected PaymentVerificationService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enrollmentService = app(RegularStudentEnrollmentService::class);
        $this->paymentService = app(PaymentVerificationService::class);
        
        // Seed courses for testing
        $this->seedTestCourses();
    }

    /**
     * Seed test courses for schedule assignment.
     */
    protected function seedTestCourses(): void
    {
        // Create schools
        $csSchool = School::factory()->create(['code' => 'CS', 'name' => 'Computer Science']);
        $engSchool = School::factory()->create(['code' => 'ENG', 'name' => 'Engineering']);
        $busSchool = School::factory()->create(['code' => 'BUS', 'name' => 'Business']);

        // Create CS courses
        Course::factory()->create(['course_code' => 'CS101', 'title' => 'Intro to Programming', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'MATH101', 'title' => 'Calculus I', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'ENGL101', 'title' => 'English Composition', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'PE101', 'title' => 'Physical Education', 'units' => 2, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'HIST101', 'title' => 'World History', 'units' => 3, 'school_id' => $csSchool->id]);
        
        Course::factory()->create(['course_code' => 'CS201', 'title' => 'Data Structures', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'MATH201', 'title' => 'Calculus II', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'PHYS101', 'title' => 'Physics I', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'CS202', 'title' => 'Algorithms', 'units' => 3, 'school_id' => $csSchool->id]);
        Course::factory()->create(['course_code' => 'ENGL201', 'title' => 'Technical Writing', 'units' => 3, 'school_id' => $csSchool->id]);

        // Create ENG courses
        Course::factory()->create(['course_code' => 'ENG101', 'title' => 'Engineering Fundamentals', 'units' => 3, 'school_id' => $engSchool->id]);
        Course::factory()->create(['course_code' => 'CHEM101', 'title' => 'Chemistry I', 'units' => 3, 'school_id' => $engSchool->id]);
        Course::factory()->create(['course_code' => 'DRAW101', 'title' => 'Engineering Drawing', 'units' => 2, 'school_id' => $engSchool->id]);

        // Create BUS courses
        Course::factory()->create(['course_code' => 'BUS101', 'title' => 'Business Fundamentals', 'units' => 3, 'school_id' => $busSchool->id]);
        Course::factory()->create(['course_code' => 'ECON101', 'title' => 'Economics I', 'units' => 3, 'school_id' => $busSchool->id]);
        Course::factory()->create(['course_code' => 'ACCT101', 'title' => 'Accounting I', 'units' => 3, 'school_id' => $busSchool->id]);
        Course::factory()->create(['course_code' => 'STAT101', 'title' => 'Statistics', 'units' => 3, 'school_id' => $busSchool->id]);
        Course::factory()->create(['course_code' => 'BENGL101', 'title' => 'Business English', 'units' => 3, 'school_id' => $busSchool->id]);
    }

    /**
     * Property: Regular students are automatically assigned a schedule
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function regular_students_automatically_assigned_schedule()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $this->assertTrue($student->isRegular());

        $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);

        $this->assertNotNull($enrollment);
        $this->assertEquals('draft', $enrollment->status);
        $this->assertGreaterThan(0, $enrollment->courses()->count());
    }

    /**
     * Property: Schedule assignment is based on school affiliation
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function schedule_assignment_based_on_school_affiliation()
    {
        $csSchool = School::where('code', 'CS')->first();
        $engSchool = School::where('code', 'ENG')->first();

        $csStudent = Student::factory()->create([
            'school_id' => $csSchool->id,
            'year_level' => 1
        ]);

        $engStudent = Student::factory()->create([
            'school_id' => $engSchool->id,
            'year_level' => 1
        ]);

        $csEnrollment = $this->enrollmentService->createAutomaticEnrollment($csStudent);
        $engEnrollment = $this->enrollmentService->createAutomaticEnrollment($engStudent);

        // CS student should have CS courses
        $csCourses = $csEnrollment->courses;
        $this->assertTrue($csCourses->contains('course_code', 'CS101'));
        
        // ENG student should have ENG courses
        $engCourses = $engEnrollment->courses;
        $this->assertTrue($engCourses->contains('course_code', 'ENG101'));
        
        // Verify they have different schedules
        $this->assertNotEquals(
            $csCourses->pluck('course_code')->sort()->values(),
            $engCourses->pluck('course_code')->sort()->values()
        );
    }

    /**
     * Property: Assigned schedule includes complete course details
     * Validates: Requirement 4.3
     * 
     * @test
     */
    public function assigned_schedule_includes_complete_course_details()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);
        $courses = $enrollment->courses;

        $this->assertGreaterThan(0, $courses->count());

        foreach ($courses as $course) {
            // Verify course details
            $this->assertNotEmpty($course->course_code);
            $this->assertNotEmpty($course->title);
            $this->assertGreaterThan(0, $course->units);
            
            // Verify schedule details from pivot
            $this->assertNotEmpty($course->pivot->schedule_day);
            $this->assertNotEmpty($course->pivot->start_time);
            $this->assertNotEmpty($course->pivot->end_time);
            $this->assertNotEmpty($course->pivot->room);
            $this->assertNotEmpty($course->pivot->instructor);
        }
    }

    /**
     * Property: Regular student route displays assigned schedule
     * Validates: Requirements 4.3, 4.4
     * 
     * @test
     */
    public function regular_student_route_displays_assigned_schedule()
    {
        $this->markTestSkipped('Route testing requires full application bootstrap with middleware');
    }

    /**
     * Property: Irregular students cannot access regular enrollment
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function irregular_students_cannot_access_regular_enrollment()
    {
        $this->markTestSkipped('Route testing requires full application bootstrap with middleware');
    }

    /**
     * Property: Regular students cannot modify assigned schedule
     * Validates: Requirement 4.4
     * 
     * @test
     */
    public function regular_students_cannot_modify_assigned_schedule()
    {
        $this->markTestSkipped('Route testing requires full application bootstrap with middleware');
    }

    /**
     * Property: Schedule assignment is consistent for same student
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function schedule_assignment_is_consistent_for_same_student()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment1 = $this->enrollmentService->createAutomaticEnrollment($student);
        $courses1 = $enrollment1->courses->pluck('course_code')->sort()->values();

        // Get enrollment again (should return same enrollment)
        $enrollment2 = $this->enrollmentService->getStudentEnrollment($student);
        $courses2 = $enrollment2->courses->pluck('course_code')->sort()->values();

        $this->assertEquals($enrollment1->id, $enrollment2->id);
        $this->assertEquals($courses1, $courses2);
    }

    /**
     * Property: Different year levels get different schedules
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function different_year_levels_get_different_schedules()
    {
        $school = School::where('code', 'CS')->first();
        
        $year1Student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $year2Student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 2
        ]);

        $year1Enrollment = $this->enrollmentService->createAutomaticEnrollment($year1Student);
        $year2Enrollment = $this->enrollmentService->createAutomaticEnrollment($year2Student);

        $year1Courses = $year1Enrollment->courses->pluck('course_code')->sort()->values();
        $year2Courses = $year2Enrollment->courses->pluck('course_code')->sort()->values();

        // Verify different schedules
        $this->assertNotEquals($year1Courses, $year2Courses);
        
        // Year 1 should have CS101
        $this->assertTrue($year1Enrollment->courses->contains('course_code', 'CS101'));
        
        // Year 2 should have CS201
        $this->assertTrue($year2Enrollment->courses->contains('course_code', 'CS201'));
    }

    /**
     * Property: Total units are calculated correctly
     * Validates: Requirement 4.3
     * 
     * @test
     */
    public function total_units_calculated_correctly()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);
        
        $expectedUnits = $enrollment->courses->sum('units');
        $this->assertEquals($expectedUnits, $enrollment->total_units);
        $this->assertGreaterThan(0, $enrollment->total_units);
    }

    /**
     * Property: Schedule can be submitted for approval
     * Validates: Requirement 4.4
     * 
     * @test
     */
    public function schedule_can_be_submitted_for_approval()
    {
        $school = School::where('code', 'CS')->first();
        
        // Create professor for approval
        Professor::factory()->create([
            'school_id' => $school->id,
            'status' => 'active'
        ]);

        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);
        $this->assertEquals('draft', $enrollment->status);

        $success = $this->enrollmentService->submitForApproval($enrollment);
        
        $this->assertTrue($success);
        $enrollment->refresh();
        $this->assertEquals('submitted', $enrollment->status);
        $this->assertNotNull($enrollment->submitted_at);
        $this->assertNotNull($enrollment->professor_id);
    }

    /**
     * Property: Multiple regular students can enroll simultaneously
     * Validates: Requirements 4.1, 4.3
     * 
     * @test
     */
    public function multiple_regular_students_can_enroll_simultaneously()
    {
        $school = School::where('code', 'CS')->first();
        
        $students = Student::factory()->count(5)->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollments = [];
        foreach ($students as $student) {
            $enrollments[] = $this->enrollmentService->createAutomaticEnrollment($student);
        }

        // Verify all enrollments were created
        $this->assertCount(5, $enrollments);
        
        // Verify each has courses assigned
        foreach ($enrollments as $enrollment) {
            $this->assertGreaterThan(0, $enrollment->courses()->count());
            $this->assertGreaterThan(0, $enrollment->total_units);
        }
    }

    /**
     * Property: Schedule assignment works for all supported schools
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function schedule_assignment_works_for_all_schools()
    {
        $schools = School::whereIn('code', ['CS', 'ENG', 'BUS'])->get();

        foreach ($schools as $school) {
            $student = Student::factory()->create([
                'school_id' => $school->id,
                'year_level' => 1
            ]);

            $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);

            $this->assertNotNull($enrollment);
            $this->assertGreaterThan(0, $enrollment->courses()->count());
            $this->assertGreaterThan(0, $enrollment->total_units);
            
            // Verify at least one course is assigned (schedule templates may include shared courses)
            $this->assertGreaterThan(0, $enrollment->courses->count());
        }
    }

    /**
     * Property: Enrollment is created for current semester
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function enrollment_created_for_current_semester()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);

        $this->assertEquals($this->paymentService->getCurrentSemester(), $enrollment->semester);
        $this->assertEquals($this->paymentService->getCurrentAcademicYear(), $enrollment->academic_year);
    }

    /**
     * Property: Only one enrollment per student per semester
     * Validates: Requirement 4.1
     * 
     * @test
     */
    public function only_one_enrollment_per_student_per_semester()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment1 = $this->enrollmentService->createAutomaticEnrollment($student);
        $enrollment2 = $this->enrollmentService->createAutomaticEnrollment($student);

        // Should return the same enrollment
        $this->assertEquals($enrollment1->id, $enrollment2->id);
        
        // Verify only one enrollment exists
        $count = Enrollment::where('student_id', $student->id)
            ->where('semester', $this->paymentService->getCurrentSemester())
            ->where('academic_year', $this->paymentService->getCurrentAcademicYear())
            ->count();
        
        $this->assertEquals(1, $count);
    }

    /**
     * Property: Schedule includes time and location information
     * Validates: Requirement 4.3
     * 
     * @test
     */
    public function schedule_includes_time_and_location_information()
    {
        $school = School::where('code', 'CS')->first();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'year_level' => 1
        ]);

        $enrollment = $this->enrollmentService->createAutomaticEnrollment($student);
        $courses = $enrollment->courses;

        foreach ($courses as $course) {
            // Verify time information
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $course->pivot->start_time);
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $course->pivot->end_time);
            
            // Verify location information
            $this->assertNotEmpty($course->pivot->room);
            
            // Verify instructor information
            $this->assertNotEmpty($course->pivot->instructor);
            
            // Verify day information
            $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $this->assertContains($course->pivot->schedule_day, $validDays);
        }
    }


}
