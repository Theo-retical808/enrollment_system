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
class AuthenticationPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property: Only authenticated students can access student dashboard
     * 
     * @test
     */
    public function authenticated_students_can_access_dashboard()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'password' => Hash::make('password123')
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('student.dashboard');
    }

    /**
     * Property: Unauthenticated users are redirected to login
     * 
     * @test
     */
    public function unauthenticated_users_redirected_to_login()
    {
        $response = $this->get(route('student.dashboard'));

        $response->assertRedirect(route('student.login'));
    }

    /**
     * Property: Students cannot access professor routes
     * 
     * @test
     */
    public function students_cannot_access_professor_routes()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('professor.dashboard'));

        $response->assertStatus(302); // Redirected
    }

    /**
     * Property: Professors cannot access student routes
     * 
     * @test
     */
    public function professors_cannot_access_student_routes()
    {
        $school = School::factory()->create();
        $professor = Professor::factory()->create([
            'school_id' => $school->id
        ]);

        $response = $this->actingAs($professor, 'professor')
            ->get(route('student.dashboard'));

        $response->assertStatus(302); // Redirected
    }

    /**
     * Property: Valid credentials allow successful login
     * 
     * @test
     */
    public function valid_credentials_allow_login()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-001',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-001',
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student, 'student');
    }

    /**
     * Property: Invalid credentials prevent login
     * 
     * @test
     */
    public function invalid_credentials_prevent_login()
    {
        $school = School::factory()->create();
        Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-001',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-001',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest('student');
    }

    /**
     * Property: Logout clears authentication
     * 
     * @test
     */
    public function logout_clears_authentication()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id
        ]);

        $this->actingAs($student, 'student');
        $this->assertAuthenticatedAs($student, 'student');

        $response = $this->post(route('student.logout'));

        $response->assertRedirect(route('student.login'));
        $this->assertGuest('student');
    }

    /**
     * Property: Suspended accounts cannot login
     * Validates: Requirement 1.3
     * 
     * @test
     */
    public function suspended_accounts_cannot_login()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-002',
            'password' => Hash::make('password123'),
            'status' => 'suspended'
        ]);

        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-002',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrorsIn('default', ['student_id']);
        $this->assertGuest('student');
        
        // Verify error message contains account status
        $errors = session('errors');
        $this->assertStringContainsString('suspended', $errors->first('student_id'));
    }

    /**
     * Property: Inactive accounts cannot login
     * Validates: Requirement 1.3
     * 
     * @test
     */
    public function inactive_accounts_cannot_login()
    {
        $school = School::factory()->create();
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-003',
            'password' => Hash::make('password123'),
            'status' => 'inactive'
        ]);

        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-003',
            'password' => 'password123'
        ]);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrorsIn('default', ['student_id']);
        $this->assertGuest('student');
        
        // Verify error message contains account status
        $errors = session('errors');
        $this->assertStringContainsString('inactive', $errors->first('student_id'));
    }

    /**
     * Property: Only active accounts can login successfully
     * Validates: Requirement 1.3
     * 
     * @test
     */
    public function only_active_accounts_can_login()
    {
        $school = School::factory()->create();
        
        // Create students with different statuses
        $activeStudent = Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-004',
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);

        $suspendedStudent = Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-005',
            'password' => Hash::make('password123'),
            'status' => 'suspended'
        ]);

        $inactiveStudent = Student::factory()->create([
            'school_id' => $school->id,
            'student_id' => 'TEST-2024-006',
            'password' => Hash::make('password123'),
            'status' => 'inactive'
        ]);

        // Active student should login successfully
        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-004',
            'password' => 'password123'
        ]);
        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($activeStudent, 'student');
        
        // Logout
        $this->post(route('student.logout'));

        // Suspended student should be rejected
        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-005',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors();
        $this->assertGuest('student');

        // Inactive student should be rejected
        $response = $this->post(route('student.login'), [
            'student_id' => 'TEST-2024-006',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors();
        $this->assertGuest('student');
    }
}
