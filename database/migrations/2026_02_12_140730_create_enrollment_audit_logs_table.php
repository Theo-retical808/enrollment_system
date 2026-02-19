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
        Schema::create('enrollment_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->comment('Professor or admin who performed the action');
            $table->string('user_type')->nullable()->comment('professor, student, or system');
            $table->string('action')->comment('submitted, approved, rejected, modified, etc.');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('comments')->nullable();
            $table->json('metadata')->nullable()->comment('Additional context like course changes, validation results');
            $table->timestamp('action_timestamp')->useCurrent();
            $table->timestamps();
            
            $table->index(['enrollment_id', 'action_timestamp']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_audit_logs');
    }
};
