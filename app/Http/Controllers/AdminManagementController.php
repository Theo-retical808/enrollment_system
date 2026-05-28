<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CurriculumTemplate;
use App\Models\Professor;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminManagementController extends Controller
{
    // ─── PROFESSORS ─────────────────────────────────────────────────────

    /**
     * Show the professor management page.
     */
    public function professors(Request $request)
    {
        $query = Professor::with('school');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('professor_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $professors = $query->orderBy('professor_id')->paginate(20);
        $schools = School::where('is_active', true)->orderBy('name')->get();

        return view('admin.professors.index', compact('professors', 'schools'));
    }

    /**
     * Show the form to create a new professor.
     */
    public function createProfessor()
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        return view('admin.professors.create', compact('schools'));
    }

    /**
     * Store a new professor.
     */
    public function storeProfessor(Request $request)
    {
        $request->validate([
            'professor_id' => 'required|string|max:20|unique:professors,professor_id',
            'email' => 'required|email|max:255|unique:professors,email',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'school_id' => 'required|exists:schools,id',
            'can_assist_enrollment' => 'boolean',
        ]);

        $professor = Professor::create([
            'professor_id' => $request->professor_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'school_id' => $request->school_id,
            'status' => 'active',
            'can_assist_enrollment' => $request->boolean('can_assist_enrollment'),
        ]);

        Log::info('Professor created by admin', [
            'professor_id' => $professor->professor_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.professors.index')
            ->with('success', "Professor {$professor->full_name} created successfully.");
    }

    /**
     * Show the form to edit a professor.
     */
    public function editProfessor(Professor $professor)
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        $professor->load('courses');
        return view('admin.professors.edit', compact('professor', 'schools'));
    }

    /**
     * Update a professor.
     */
    public function updateProfessor(Request $request, Professor $professor)
    {
        $request->validate([
            'professor_id' => 'required|string|max:20|unique:professors,professor_id,' . $professor->id,
            'email' => 'required|email|max:255|unique:professors,email,' . $professor->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'school_id' => 'required|exists:schools,id',
            'status' => 'required|in:active,inactive,suspended',
            'can_assist_enrollment' => 'boolean',
        ]);

        $data = $request->only(['professor_id', 'email', 'first_name', 'last_name', 'school_id', 'status']);
        $data['can_assist_enrollment'] = $request->boolean('can_assist_enrollment');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $professor->update($data);

        Log::info('Professor updated by admin', [
            'professor_id' => $professor->professor_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.professors.index')
            ->with('success', "Professor {$professor->full_name} updated successfully.");
    }

    /**
     * Delete a professor.
     */
    public function destroyProfessor(Professor $professor)
    {
        $name = $professor->full_name;
        $professor->courses()->detach();
        $professor->delete();

        Log::info('Professor deleted by admin', [
            'professor_id' => $professor->professor_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.professors.index')
            ->with('success', "Professor {$name} deleted successfully.");
    }

    // ─── STUDENTS ───────────────────────────────────────────────────────

    /**
     * Show the student management page.
     */
    public function students(Request $request)
    {
        $query = Student::with('school');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('student_id')->paginate(20);
        $schools = School::where('is_active', true)->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'schools'));
    }

    /**
     * Show the form to create a new student.
     */
    public function createStudent()
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        return view('admin.students.create', compact('schools'));
    }

    /**
     * Store a new student.
     */
    public function storeStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:20|unique:students,student_id',
            'email' => 'required|email|max:255|unique:students,email',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'school_id' => 'required|exists:schools,id',
            'year_level' => 'required|integer|min:1|max:5',
        ]);

        $student = Student::create([
            'student_id' => $request->student_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'school_id' => $request->school_id,
            'year_level' => $request->year_level,
            'status' => 'active',
        ]);

        Log::info('Student created by admin', [
            'student_id' => $student->student_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$student->full_name} created successfully.");
    }

    /**
     * Show the form to edit a student.
     */
    public function editStudent(Student $student)
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'schools'));
    }

    /**
     * Update a student.
     */
    public function updateStudent(Request $request, Student $student)
    {
        $request->validate([
            'student_id' => 'required|string|max:20|unique:students,student_id,' . $student->id,
            'email' => 'required|email|max:255|unique:students,email,' . $student->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'school_id' => 'required|exists:schools,id',
            'year_level' => 'required|integer|min:1|max:5',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $data = $request->only(['student_id', 'email', 'first_name', 'last_name', 'school_id', 'year_level', 'status']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        Log::info('Student updated by admin', [
            'student_id' => $student->student_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$student->full_name} updated successfully.");
    }

    /**
     * Delete a student.
     */
    public function destroyStudent(Student $student)
    {
        $name = $student->full_name;
        $student->delete();

        Log::info('Student deleted by admin', [
            'student_id' => $student->student_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', "Student {$name} deleted successfully.");
    }

    // ─── COURSES ────────────────────────────────────────────────────────

    /**
     * Show the course management page.
     */
    public function courses(Request $request)
    {
        $query = Course::with(['school', 'professors']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }

        $courses = $query->orderBy('course_code')->paginate(20);
        $schools = School::where('is_active', true)->orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'schools'));
    }

    /**
     * Show the form to create a new course.
     */
    public function createCourse()
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        $allCourses = Course::orderBy('course_code')->get();
        return view('admin.courses.create', compact('schools', 'allCourses'));
    }

    /**
     * Store a new course.
     */
    public function storeCourse(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:6',
            'school_id' => 'required|exists:schools,id',
            'year_level' => 'nullable|integer|min:1|max:5',
            'semester' => 'nullable|in:1,2,summer',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
        ]);

        $course = Course::create([
            'course_code' => $request->course_code,
            'title' => $request->title,
            'description' => $request->description,
            'units' => $request->units,
            'school_id' => $request->school_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'is_active' => true,
        ]);

        if ($request->prerequisites) {
            $course->prerequisites()->sync($request->prerequisites);
        }

        Log::info('Course created by admin', [
            'course_code' => $course->course_code,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course {$course->course_code} - {$course->title} created successfully.");
    }

    /**
     * Show the form to edit a course.
     */
    public function editCourse(Course $course)
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        $allCourses = Course::where('id', '!=', $course->id)->orderBy('course_code')->get();
        $course->load(['prerequisites', 'professors']);
        return view('admin.courses.edit', compact('course', 'schools', 'allCourses'));
    }

    /**
     * Update a course.
     */
    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1|max:6',
            'school_id' => 'required|exists:schools,id',
            'year_level' => 'nullable|integer|min:1|max:5',
            'semester' => 'nullable|in:1,2,summer',
            'is_active' => 'boolean',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
        ]);

        $course->update([
            'course_code' => $request->course_code,
            'title' => $request->title,
            'description' => $request->description,
            'units' => $request->units,
            'school_id' => $request->school_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'is_active' => $request->boolean('is_active'),
        ]);

        $course->prerequisites()->sync($request->prerequisites ?? []);

        Log::info('Course updated by admin', [
            'course_code' => $course->course_code,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course {$course->course_code} updated successfully.");
    }

    /**
     * Delete a course.
     */
    public function destroyCourse(Course $course)
    {
        $code = $course->course_code;
        $course->prerequisites()->detach();
        $course->professors()->detach();
        $course->delete();

        Log::info('Course deleted by admin', [
            'course_code' => $code,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course {$code} deleted successfully.");
    }

    // ─── COURSE-PROFESSOR ASSIGNMENTS ───────────────────────────────────

    /**
     * Show the course-professor assignment page.
     */
    public function assignments(Request $request)
    {
        $courses = Course::with(['professors', 'school'])->orderBy('course_code')->get();
        $professors = Professor::with('school')->where('status', 'active')->orderBy('last_name')->get();

        return view('admin.assignments.index', compact('courses', 'professors'));
    }

    /**
     * Assign a professor to a course.
     */
    public function assignProfessor(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:professors,id',
            'role' => 'required|in:instructor,assistant',
        ]);

        $course = Course::findOrFail($request->course_id);
        $professor = Professor::findOrFail($request->professor_id);

        // Check if already assigned
        if ($course->professors()->where('professor_id', $professor->id)->exists()) {
            return redirect()->back()->with('error', "Professor {$professor->full_name} is already assigned to {$course->course_code}.");
        }

        $course->professors()->attach($professor->id, ['role' => $request->role]);

        Log::info('Professor assigned to course by admin', [
            'course_id' => $course->id,
            'professor_id' => $professor->id,
            'role' => $request->role,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->back()
            ->with('success', "Professor {$professor->full_name} assigned to {$course->course_code} as {$request->role}.");
    }

    /**
     * Remove a professor from a course.
     */
    public function unassignProfessor(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:professors,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $professor = Professor::findOrFail($request->professor_id);

        $course->professors()->detach($professor->id);

        Log::info('Professor unassigned from course by admin', [
            'course_id' => $course->id,
            'professor_id' => $professor->id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->back()
            ->with('success', "Professor {$professor->full_name} removed from {$course->course_code}.");
    }

    // ─── ENROLLMENT ASSISTANTS ──────────────────────────────────────────

    /**
     * Show the enrollment assistants management page.
     */
    public function enrollmentAssistants(Request $request)
    {
        $assistants = Professor::where('can_assist_enrollment', true)
            ->with('school')
            ->orderBy('last_name')
            ->get();

        $availableProfessors = Professor::where('can_assist_enrollment', false)
            ->where('status', 'active')
            ->with('school')
            ->orderBy('last_name')
            ->get();

        return view('admin.assignments.enrollment-assistants', compact('assistants', 'availableProfessors'));
    }

    /**
     * Toggle a professor's enrollment assistant status.
     */
    public function toggleEnrollmentAssistant(Request $request, Professor $professor)
    {
        $professor->update([
            'can_assist_enrollment' => !$professor->can_assist_enrollment,
        ]);

        $status = $professor->can_assist_enrollment ? 'designated as' : 'removed from';

        Log::info("Professor {$status} enrollment assistant by admin", [
            'professor_id' => $professor->professor_id,
            'can_assist_enrollment' => $professor->can_assist_enrollment,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->back()
            ->with('success', "Professor {$professor->full_name} {$status} enrollment assistant.");
    }

    // ─── COURSE SCHEDULES ───────────────────────────────────────────────

    /**
     * Show the schedule management page.
     */
    public function schedules(Request $request)
    {
        $query = CourseSchedule::with(['course', 'professor']);

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            })->orWhereHas('professor', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get();
        $courses = Course::where('is_active', true)->orderBy('course_code')->get();
        $professors = Professor::where('status', 'active')->orderBy('last_name')->get();

        return view('admin.schedules.index', compact('schedules', 'courses', 'professors'));
    }

    /**
     * Store a new course schedule.
     */
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'required|exists:professors,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'required|string|max:50',
            'max_students' => 'nullable|integer|min:1|max:100',
        ]);

        // Check for professor time conflict
        $conflict = CourseSchedule::where('professor_id', $request->professor_id)
            ->where('day', $request->day)
            ->where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })->first();

        if ($conflict) {
            return redirect()->back()->withInput()
                ->with('error', "Schedule conflict: Professor already has {$conflict->course->course_code} on {$conflict->day} at {$conflict->time_range}.");
        }

        // Check for room conflict
        $roomConflict = CourseSchedule::where('room', $request->room)
            ->where('day', $request->day)
            ->where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })->first();

        if ($roomConflict) {
            return redirect()->back()->withInput()
                ->with('error', "Room conflict: {$request->room} is already booked for {$roomConflict->course->course_code} on {$roomConflict->day} at {$roomConflict->time_range}.");
        }

        $schedule = CourseSchedule::create([
            'course_id' => $request->course_id,
            'professor_id' => $request->professor_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
            'max_students' => $request->max_students ?? 40,
        ]);

        $course = Course::find($request->course_id);
        $professor = Professor::find($request->professor_id);

        Log::info('Course schedule created by admin', [
            'schedule_id' => $schedule->id,
            'course_code' => $course->course_code,
            'professor_id' => $professor->professor_id,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', "Schedule added: {$course->course_code} with {$professor->full_name} on {$request->day} ({$schedule->time_range}).");
    }

    /**
     * Delete a course schedule.
     */
    public function destroySchedule(CourseSchedule $schedule)
    {
        $info = "{$schedule->course->course_code} - {$schedule->day} {$schedule->time_range}";
        $schedule->delete();

        Log::info('Course schedule deleted by admin', [
            'info' => $info,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', "Schedule removed: {$info}.");
    }

    // ─── CURRICULUM TEMPLATES (Default Regular Schedules) ────────────────

    /**
     * Show curriculum template management page.
     */
    public function curriculum(Request $request)
    {
        $schools = School::where('is_active', true)->orderBy('name')->get();
        $selectedSchool = $request->school_id ? School::find($request->school_id) : $schools->first();

        $templates = collect();
        if ($selectedSchool) {
            $templates = CurriculumTemplate::where('school_id', $selectedSchool->id)
                ->where('is_active', true)
                ->with(['course', 'courseSchedule.professor'])
                ->orderBy('year_level')
                ->orderBy('semester')
                ->get()
                ->groupBy(function ($item) {
                    return "Year {$item->year_level} - {$item->semester}";
                });
        }

        $courses = Course::where('is_active', true)->orderBy('course_code')->get();
        $schedules = CourseSchedule::where('is_active', true)->with(['course', 'professor'])->get();

        return view('admin.curriculum.index', compact('schools', 'selectedSchool', 'templates', 'courses', 'schedules'));
    }

    /**
     * Add a course to the curriculum template.
     */
    public function storeCurriculum(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'year_level' => 'required|integer|min:1|max:5',
            'semester' => 'required|in:1st Semester,2nd Semester,Summer',
            'course_id' => 'required|exists:courses,id',
            'course_schedule_id' => 'nullable|exists:course_schedules,id',
        ]);

        // Check if already exists
        $exists = CurriculumTemplate::where('school_id', $request->school_id)
            ->where('year_level', $request->year_level)
            ->where('semester', $request->semester)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This course is already in the curriculum for that year/semester.');
        }

        CurriculumTemplate::create([
            'school_id' => $request->school_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'course_id' => $request->course_id,
            'course_schedule_id' => $request->course_schedule_id,
        ]);

        $course = Course::find($request->course_id);

        Log::info('Curriculum template entry added', [
            'school_id' => $request->school_id,
            'year_level' => $request->year_level,
            'semester' => $request->semester,
            'course_code' => $course->course_code,
            'admin_id' => Auth::guard('admin')->user()->id,
        ]);

        return redirect()->back()->with('success', "Added {$course->course_code} to Year {$request->year_level} - {$request->semester} curriculum.");
    }

    /**
     * Remove a course from the curriculum template.
     */
    public function destroyCurriculum(CurriculumTemplate $template)
    {
        $info = "{$template->course->course_code} (Year {$template->year_level} - {$template->semester})";
        $template->delete();

        return redirect()->back()->with('success', "Removed {$info} from curriculum.");
    }
}
