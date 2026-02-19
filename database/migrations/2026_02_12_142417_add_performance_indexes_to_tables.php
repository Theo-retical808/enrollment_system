<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Students table indexes
        Schema::table('students', function (Blueprint $table) {
            $table->index('school_id'); // For filtering students by school
            $table->index('status'); // For filtering active/inactive students
            $table->index(['school_id', 'year_level']); // Composite for school + year queries
        });

        // Courses table indexes
        Schema::table('courses', function (Blueprint $table) {
            $table->index('school_id'); // For filtering courses by school
            $table->index('is_active'); // For filtering active courses
            $table->index(['school_id', 'is_active']); // Composite for active courses by school
            $table->index('title'); // For course search by title
        });

        // Enrollments table indexes
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index('student_id'); // Already has foreign key, but explicit index helps
            $table->index('status'); // For filtering by enrollment status
            $table->index('professor_id'); // For professor review queue
            $table->index(['semester', 'academic_year']); // For filtering by term
            $table->index(['student_id', 'semester', 'academic_year']); // Composite for student term lookup
            $table->index(['status', 'professor_id']); // For professor pending reviews
        });

        // Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index('student_id'); // Already has foreign key
            $table->index('status'); // For filtering payment status
            $table->index(['student_id', 'semester', 'academic_year', 'payment_type']); // Composite for payment verification
            $table->index(['semester', 'academic_year', 'status']); // For term payment reports
        });

        // Enrollment courses table indexes
        Schema::table('enrollment_courses', function (Blueprint $table) {
            $table->index('enrollment_id'); // Already has foreign key
            $table->index('course_id'); // Already has foreign key
            $table->index(['enrollment_id', 'schedule_day']); // For schedule conflict detection
            $table->index(['schedule_day', 'start_time', 'end_time']); // For time-based queries
        });

        // Course prerequisites table indexes
        Schema::table('course_prerequisites', function (Blueprint $table) {
            $table->index('course_id'); // For prerequisite lookups
            $table->index('prerequisite_id'); // For reverse prerequisite lookups
        });

        // Student completed courses table indexes
        Schema::table('student_completed_courses', function (Blueprint $table) {
            $table->index('student_id'); // For student course history
            $table->index('course_id'); // For course completion tracking
            $table->index(['student_id', 'grade']); // For failed course detection
        });

        // Petitions table indexes (if exists)
        if (Schema::hasTable('petitions')) {
            Schema::table('petitions', function (Blueprint $table) {
                $table->index('student_id'); // For student petition history
                $table->index('status'); // For filtering petition status
                $table->index(['student_id', 'status']); // Composite for student active petitions
            });
        }

        // Enrollment audit logs table indexes
        if (Schema::hasTable('enrollment_audit_logs')) {
            Schema::table('enrollment_audit_logs', function (Blueprint $table) {
                $table->index('enrollment_id'); // For enrollment audit trail
                $table->index('created_at'); // For chronological queries
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['school_id', 'year_level']);
        });

        // Courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['school_id', 'is_active']);
            $table->dropIndex(['title']);
        });

        // Enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['professor_id']);
            $table->dropIndex(['semester', 'academic_year']);
            $table->dropIndex(['student_id', 'semester', 'academic_year']);
            $table->dropIndex(['status', 'professor_id']);
        });

        // Payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['student_id', 'semester', 'academic_year', 'payment_type']);
            $table->dropIndex(['semester', 'academic_year', 'status']);
        });

        // Enrollment courses table
        Schema::table('enrollment_courses', function (Blueprint $table) {
            $table->dropIndex(['enrollment_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['enrollment_id', 'schedule_day']);
            $table->dropIndex(['schedule_day', 'start_time', 'end_time']);
        });

        // Course prerequisites table
        Schema::table('course_prerequisites', function (Blueprint $table) {
            $table->dropIndex(['course_id']);
            $table->dropIndex(['prerequisite_id']);
        });

        // Student completed courses table
        Schema::table('student_completed_courses', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['course_id']);
            $table->dropIndex(['student_id', 'grade']);
        });

        // Petitions table
        if (Schema::hasTable('petitions')) {
            Schema::table('petitions', function (Blueprint $table) {
                $table->dropIndex(['student_id']);
                $table->dropIndex(['status']);
                $table->dropIndex(['student_id', 'status']);
            });
        }

        // Enrollment audit logs table
        if (Schema::hasTable('enrollment_audit_logs')) {
            Schema::table('enrollment_audit_logs', function (Blueprint $table) {
                $table->dropIndex(['enrollment_id']);
                $table->dropIndex(['created_at']);
            });
        }
    }
};
