<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Curriculum templates: default schedules for regular students per year/semester
        Schema::create('curriculum_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->integer('year_level'); // 1, 2, 3, 4
            $table->string('semester', 20); // 1st Semester, 2nd Semester, Summer
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('course_schedule_id')->nullable()->constrained('course_schedules')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'year_level', 'semester', 'course_id'], 'curriculum_unique');
        });

        // Add grade column to enrollment_courses for professor grading
        Schema::table('enrollment_courses', function (Blueprint $table) {
            $table->string('grade', 5)->nullable()->after('instructor');
            $table->decimal('numeric_grade', 3, 2)->nullable()->after('grade');
            $table->enum('grade_status', ['pending', 'graded'])->default('pending')->after('numeric_grade');
            $table->foreignId('graded_by')->nullable()->constrained('professors')->nullOnDelete()->after('grade_status');
            $table->timestamp('graded_at')->nullable()->after('graded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_templates');

        Schema::table('enrollment_courses', function (Blueprint $table) {
            $table->dropColumn(['grade', 'numeric_grade', 'grade_status', 'graded_by', 'graded_at']);
        });
    }
};
