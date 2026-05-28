<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfessorGradingController extends Controller
{
    /**
     * Show the grading page with all courses the professor teaches.
     */
    public function index()
    {
        $professor = Auth::guard('professor')->user();

        // Get courses this professor teaches (from course_schedules)
        $schedules = CourseSchedule::where('professor_id', $professor->id)
            ->where('is_active', true)
            ->with('course')
            ->get();

        $courseIds = $schedules->pluck('course_id')->unique();

        // Get approved enrollments that include these courses
        $enrollments = DB::table('enrollment_courses')
            ->join('enrollments', 'enrollment_courses.enrollment_id', '=', 'enrollments.id')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->join('courses', 'enrollment_courses.course_id', '=', 'courses.id')
            ->whereIn('enrollment_courses.course_id', $courseIds)
            ->where('enrollments.status', 'approved')
            ->select(
                'enrollment_courses.id as enrollment_course_id',
                'enrollment_courses.grade',
                'enrollment_courses.numeric_grade',
                'enrollment_courses.grade_status',
                'enrollment_courses.graded_at',
                'students.student_id',
                'students.first_name',
                'students.last_name',
                'courses.course_code',
                'courses.title as course_title',
                'courses.id as course_id'
            )
            ->orderBy('courses.course_code')
            ->orderBy('students.last_name')
            ->get();

        // Group by course
        $courseGroups = $enrollments->groupBy('course_code');

        return view('professor.grading', compact('courseGroups'));
    }

    /**
     * Submit a grade for a student.
     */
    public function submitGrade(Request $request)
    {
        $request->validate([
            'enrollment_course_id' => 'required|integer',
            'numeric_grade' => 'required|numeric|min:1.0|max:5.0',
        ]);

        $professor = Auth::guard('professor')->user();
        $numericGrade = $request->numeric_grade;

        // Determine letter grade and pass/fail (1.0-3.0 = pass, 3.1-5.0 = fail)
        $passed = $numericGrade <= 3.0;
        $letterGrade = $this->getLetterGrade($numericGrade);

        // Update the enrollment_courses record
        $updated = DB::table('enrollment_courses')
            ->where('id', $request->enrollment_course_id)
            ->update([
                'grade' => $letterGrade,
                'numeric_grade' => $numericGrade,
                'grade_status' => 'graded',
                'graded_by' => $professor->id,
                'graded_at' => now(),
            ]);

        if (!$updated) {
            return redirect()->back()->with('error', 'Failed to submit grade. Record not found.');
        }

        // Also update student_completed_courses if grade is final
        $record = DB::table('enrollment_courses')
            ->join('enrollments', 'enrollment_courses.enrollment_id', '=', 'enrollments.id')
            ->where('enrollment_courses.id', $request->enrollment_course_id)
            ->select('enrollments.student_id', 'enrollment_courses.course_id')
            ->first();

        if ($record) {
            // Upsert into student_completed_courses
            DB::table('student_completed_courses')->updateOrInsert(
                [
                    'student_id' => $record->student_id,
                    'course_id' => $record->course_id,
                ],
                [
                    'grade' => $letterGrade,
                    'semester' => $this->getCurrentSemester(),
                    'academic_year' => $this->getCurrentAcademicYear(),
                    'passed' => $passed,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // Clear student classification cache
            \Illuminate\Support\Facades\Cache::forget("student_is_regular_{$record->student_id}");
            \Illuminate\Support\Facades\Cache::forget("student_has_failed_courses_{$record->student_id}");
        }

        Log::info('Grade submitted by professor', [
            'professor_id' => $professor->professor_id,
            'enrollment_course_id' => $request->enrollment_course_id,
            'grade' => $letterGrade,
            'numeric_grade' => $numericGrade,
            'passed' => $passed,
        ]);

        return redirect()->back()->with('success', "Grade {$numericGrade} ({$letterGrade}) submitted successfully.");
    }

    /**
     * Convert numeric grade to letter grade.
     */
    private function getLetterGrade(float $grade): string
    {
        if ($grade == 1.0) return '1.0';
        if ($grade <= 1.25) return '1.25';
        if ($grade <= 1.5) return '1.5';
        if ($grade <= 1.75) return '1.75';
        if ($grade <= 2.0) return '2.0';
        if ($grade <= 2.25) return '2.25';
        if ($grade <= 2.5) return '2.5';
        if ($grade <= 2.75) return '2.75';
        if ($grade <= 3.0) return '3.0';
        return '5.0'; // Failed
    }

    private function getCurrentSemester(): string
    {
        $month = date('n');
        if ($month >= 6 && $month <= 10) return '1st Semester';
        if ($month >= 11 || $month <= 3) return '2nd Semester';
        return 'Summer';
    }

    private function getCurrentAcademicYear(): string
    {
        $year = date('Y');
        $month = date('n');
        return $month >= 6 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }
}
