<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\EnrollmentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfessorReviewController extends Controller
{
    /**
     * Process schedule approval or rejection.
     */
    public function processReview(Request $request, $enrollmentId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'review_comments' => 'nullable|string|max:1000',
        ]);

        $professor = Auth::guard('professor')->user();
        
        $enrollment = Enrollment::with(['student.school', 'courses'])
            ->findOrFail($enrollmentId);
        
        // Verify the enrollment is for a student in the professor's school
        if ($enrollment->student->school_id !== $professor->school_id) {
            abort(403, 'You are not authorized to review this enrollment.');
        }
        
        // Verify the enrollment is in submitted status
        if ($enrollment->status !== 'submitted') {
            return redirect()->route('professor.dashboard')
                ->with('error', 'This enrollment is not available for review.');
        }

        $action = $request->input('action');
        $comments = $request->input('review_comments');

        try {
            DB::beginTransaction();

            $oldStatus = $enrollment->status;

            if ($action === 'approve') {
                $this->approveSchedule($enrollment, $professor, $comments);
                $message = 'Schedule approved successfully.';
                $studentMessage = 'approved';
                $newStatus = 'approved';
            } else {
                $this->rejectSchedule($enrollment, $professor, $comments);
                $message = 'Schedule rejected successfully.';
                $studentMessage = 'rejected';
                $newStatus = 'draft'; // Rejection returns to draft for resubmission
            }

            // Create audit log entry
            EnrollmentAuditLog::logAction(
                enrollment: $enrollment,
                action: $action,
                userId: $professor->id,
                userType: 'professor',
                oldStatus: $oldStatus,
                newStatus: $newStatus,
                comments: $comments,
                metadata: [
                    'professor_name' => $professor->full_name,
                    'professor_email' => $professor->email,
                    'total_units' => $enrollment->total_units,
                    'course_count' => $enrollment->courses()->count(),
                ]
            );

            // Log the review action
            Log::info('Schedule review completed', [
                'enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->student_id,
                'professor_id' => $professor->id,
                'action' => $action,
                'timestamp' => now(),
            ]);

            // Send notification to student (placeholder for future implementation)
            $this->notifyStudent($enrollment->student, $studentMessage, $comments);

            DB::commit();

            return redirect()->route('professor.dashboard')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule review failed', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while processing the review. Please try again.');
        }
    }

    /**
     * Approve a student schedule and finalize enrollment.
     */
    private function approveSchedule(Enrollment $enrollment, $professor, $comments)
    {
        $enrollment->update([
            'status' => 'approved',
            'professor_id' => $professor->id,
            'review_comments' => $comments,
            'reviewed_at' => now(),
        ]);
        
        // Finalize the enrollment by locking the schedule
        $enrollment->finalize();
    }

    /**
     * Reject a student schedule and return to planning phase.
     */
    private function rejectSchedule(Enrollment $enrollment, $professor, $comments)
    {
        $enrollment->update([
            'status' => 'rejected',
            'professor_id' => $professor->id,
            'review_comments' => $comments,
            'reviewed_at' => now(),
        ]);
        
        // Enable resubmission by resetting submission timestamp
        // This allows the student to modify and resubmit
        $enrollment->enableResubmission();
    }

    /**
     * Notify student of review completion.
     * This is a placeholder for future notification implementation.
     */
    private function notifyStudent(Student $student, string $status, ?string $comments)
    {
        // Future implementation: Send email or in-app notification
        // For now, we'll just log the notification
        Log::info('Student notification sent', [
            'student_id' => $student->id,
            'status' => $status,
            'has_comments' => !empty($comments),
        ]);
    }

    /**
     * Get the review status for a specific enrollment.
     */
    public function getReviewStatus($enrollmentId)
    {
        $enrollment = Enrollment::with(['professor', 'student'])
            ->findOrFail($enrollmentId);

        return response()->json([
            'status' => $enrollment->status,
            'reviewed_at' => $enrollment->reviewed_at,
            'review_comments' => $enrollment->review_comments,
            'professor' => $enrollment->professor ? [
                'name' => $enrollment->professor->full_name,
                'email' => $enrollment->professor->email,
            ] : null,
        ]);
    }
}
