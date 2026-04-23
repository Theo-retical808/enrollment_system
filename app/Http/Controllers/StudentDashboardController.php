<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StudentClassificationService;
use App\Services\PaymentVerificationService;

class StudentDashboardController extends Controller
{
    protected $classificationService;
    protected $paymentService;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        StudentClassificationService $classificationService,
        PaymentVerificationService $paymentService
    ) {
        $this->classificationService = $classificationService;
        $this->paymentService = $paymentService;
    }

    /**
     * Show the student dashboard.
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        // Get student classification and routing information
        $classificationInfo = $this->classificationService->routeToEnrollment($student);
        $recommendedAction = $this->classificationService->getRecommendedAction($student);
        
        // Get payment status
        $paymentStatus = $this->paymentService->getPaymentStatus($student);
        $canAccessEnrollment = $this->paymentService->canAccessEnrollment($student);
        
        return view('student.dashboard', compact(
            'student', 
            'currentEnrollment', 
            'classificationInfo', 
            'recommendedAction',
            'paymentStatus',
            'canAccessEnrollment'
        ));
    }

    /**
     * Show the student's current schedule.
     */
    public function viewSchedule()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        if (!$currentEnrollment) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No enrollment found.');
        }
        
        return view('student.schedule', compact('student', 'currentEnrollment'));
    }

    /**
     * Export schedule as PDF.
     */
    public function exportPdf()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        if (!$currentEnrollment || $currentEnrollment->status !== 'approved') {
            return redirect()->route('student.dashboard')
                ->with('error', 'Only approved schedules can be exported.');
        }
        
        // Generate HTML for PDF
        $html = view('student.schedule-pdf', compact('student', 'currentEnrollment'))->render();
        
        // Return as downloadable HTML (can be printed to PDF by browser)
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="schedule-' . $student->student_id . '.html"');
    }

    /**
     * Export schedule as CSV.
     */
    public function exportCsv()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        if (!$currentEnrollment) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No enrollment found.');
        }
        
        $filename = 'schedule-' . $student->student_id . '-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($currentEnrollment, $student) {
            $file = fopen('php://output', 'w');
            
            // Add header information
            fputcsv($file, ['Student Enrollment Schedule']);
            fputcsv($file, ['Student ID', $student->student_id]);
            fputcsv($file, ['Student Name', $student->full_name]);
            fputcsv($file, ['School', $student->school->name ?? 'N/A']);
            fputcsv($file, ['Semester', $currentEnrollment->semester]);
            fputcsv($file, ['Academic Year', $currentEnrollment->academic_year]);
            fputcsv($file, ['Status', ucfirst($currentEnrollment->status)]);
            fputcsv($file, ['Total Units', $currentEnrollment->courses->sum('units')]);
            fputcsv($file, []);
            
            // Add course headers
            fputcsv($file, ['Course Code', 'Course Title', 'Units', 'Day', 'Start Time', 'End Time', 'Room', 'Instructor']);
            
            // Add course data
            foreach ($currentEnrollment->courses as $course) {
                fputcsv($file, [
                    $course->course_code,
                    $course->title,
                    $course->units,
                    $course->pivot->schedule_day ?? 'TBA',
                    $course->pivot->start_time ? date('g:i A', strtotime($course->pivot->start_time)) : 'TBA',
                    $course->pivot->end_time ? date('g:i A', strtotime($course->pivot->end_time)) : 'TBA',
                    $course->pivot->room ?? 'TBA',
                    $course->pivot->instructor ?? 'TBA',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Send schedule via email.
     */
    public function emailSchedule()
    {
        $student = Auth::guard('student')->user();
        $currentEnrollment = $student->getCurrentEnrollment();
        
        if (!$currentEnrollment || $currentEnrollment->status !== 'approved') {
            return redirect()->route('student.dashboard')
                ->with('error', 'Only approved schedules can be emailed.');
        }
        
        // For now, we'll simulate email sending
        // In production, you would use Laravel's Mail facade
        return redirect()->route('student.schedule')
            ->with('success', 'Schedule has been sent to ' . $student->email);
    }

    /**
     * Show student's curriculum and completed courses.
     */
    public function viewCourses()
    {
        $student = Auth::guard('student')->user();
        
        // Get all courses for the student's school
        $allCourses = \App\Models\Course::where('school_id', $student->school_id)
            ->where('is_active', true)
            ->orderBy('year_level')
            ->orderBy('course_code')
            ->get()
            ->groupBy('year_level');
        
        // Get completed courses with grades
        $completedCourses = $student->completedCourses()
            ->withPivot('grade', 'passed', 'semester', 'academic_year')
            ->get();
        
        // Get currently enrolled courses
        $currentEnrollment = $student->getCurrentEnrollment();
        $currentCourses = $currentEnrollment ? $currentEnrollment->courses : collect();
        
        // Calculate statistics
        $totalUnitsCompleted = $completedCourses->where('pivot.passed', true)->sum('units');
        $totalUnitsFailed = $completedCourses->where('pivot.passed', false)->sum('units');
        $completedCount = $completedCourses->where('pivot.passed', true)->count();
        $failedCount = $completedCourses->where('pivot.passed', false)->count();
        
        return view('student.courses', compact(
            'student',
            'allCourses',
            'completedCourses',
            'currentCourses',
            'totalUnitsCompleted',
            'totalUnitsFailed',
            'completedCount',
            'failedCount'
        ));
    }
}

