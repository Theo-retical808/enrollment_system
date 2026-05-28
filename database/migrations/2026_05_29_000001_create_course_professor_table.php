<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_professor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('professor_id')->constrained('professors')->onDelete('cascade');
            $table->string('role')->default('instructor'); // instructor, assistant
            $table->timestamps();

            $table->unique(['course_id', 'professor_id']);
        });

        // Add enrollment assistant flag to professors
        Schema::table('professors', function (Blueprint $table) {
            $table->boolean('can_assist_enrollment')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_professor');

        Schema::table('professors', function (Blueprint $table) {
            $table->dropColumn('can_assist_enrollment');
        });
    }
};
