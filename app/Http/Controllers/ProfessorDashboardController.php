<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorDashboardController extends Controller
{
    /**
     * Display the professor dashboard with pending review queue.
     */
    public function index()
    {
        $professor = Auth::guard('professor')->user();
        
        // Get pending enrollments for students in the professor's school
        $pendingEnrollments = Enrollment::where('status', 'submitted')
            ->whereHas('student', function ($query) use ($professor) {
                $query->where('school_id', $professor->school_id);
            })
            ->with(['student.school', 'courses'])
            ->orderBy('submitted_at', 'asc')
            ->get();
        
        // Get recently reviewed enrollments
        $recentlyReviewed = Enrollment::whereIn('status', ['approved', 'rejected'])
            ->where('professor_id', $professor->id)
            ->with(['student.school', 'courses'])
            ->orderBy('reviewed_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('professor.dashboard', compact('pendingEnrollments', 'recentlyReviewed'));
    }

    /**
     * Display the schedule review interface for a specific enrollment.
     */
    public function reviewSchedule($enrollmentId)
    {
        $professor = Auth::guard('professor')->user();
        
        $enrollment = Enrollment::with([
            'student.school',
            'student.completedCourses',
            'courses' => function ($query) {
                $query->orderBy('schedule_day')->orderBy('start_time');
            }
        ])->findOrFail($enrollmentId);
        
        // Verify the enrollment is for a student in the professor's school
        if ($enrollment->student->school_id !== $professor->school_id) {
            abort(403, 'You are not authorized to review this enrollment.');
        }
        
        // Verify the enrollment is in submitted status
        if ($enrollment->status !== 'submitted') {
            return redirect()->route('professor.dashboard')
                ->with('error', 'This enrollment is not available for review.');
        }
        
        // Get validation data if available
        $validationData = $enrollment->validation_data ?? [];
        
        return view('professor.review-schedule', compact('enrollment', 'validationData'));
    }
}
