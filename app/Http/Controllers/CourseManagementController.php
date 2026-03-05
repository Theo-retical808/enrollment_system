<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\School;
use Illuminate\Http\Request;

class CourseManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('school');
        
        // Filter by school
        if ($request->has('school_id') && $request->school_id) {
            $query->where('school_id', $request->school_id);
        }
        
        // Filter by year level
        if ($request->has('year_level') && $request->year_level) {
            $query->where('year_level', $request->year_level);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        
        $courses = $query->orderBy('course_code')->paginate(20);
        $schools = School::all();
        
        return view('courses.index', compact('courses', 'schools'));
    }
    
    public function show($id)
    {
        $course = Course::with(['school', 'prerequisites'])->findOrFail($id);
        
        // Get students enrolled in this course
        $enrolledStudents = $course->enrollments()
            ->with('student')
            ->whereIn('status', ['submitted', 'approved'])
            ->get()
            ->pluck('student')
            ->unique('id');
        
        return view('courses.show', compact('course', 'enrolledStudents'));
    }
}
