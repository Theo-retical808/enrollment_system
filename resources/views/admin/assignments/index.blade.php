@extends('layouts.admin')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1>Course-Professor Assignments</h1>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Assign professors to courses and manage enrollment assistants</p>
    </div>
    <a href="{{ route('admin.enrollment-assistants.index') }}" class="btn btn-secondary">Enrollment Assistants</a>
</div>

<!-- Assign Professor Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2>Assign Professor to Course</h2>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.assignments.assign') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Course *</label>
                <select name="course_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Professor *</label>
                <select name="professor_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    <option value="">Select Professor</option>
                    @foreach($professors as $professor)
                        <option value="{{ $professor->id }}">{{ $professor->full_name }} ({{ $professor->school->name ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 150px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Role *</label>
                <select name="role" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    <option value="instructor">Instructor</option>
                    <option value="assistant">Assistant</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
    </div>
</div>

<!-- Current Assignments -->
<div class="card">
    <div class="card-header">
        <h2>Current Assignments</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Course</th>
                <th>School</th>
                <th>Assigned Professors</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
                @if($course->professors->count() > 0)
                <tr>
                    <td>
                        <code class="id-badge id-badge-course">{{ $course->course_code }}</code>
                        <span style="margin-left: 0.5rem;">{{ $course->title }}</span>
                    </td>
                    <td>{{ $course->school->name ?? 'N/A' }}</td>
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach($course->professors as $prof)
                                <span class="pill-tag">
                                    {{ $prof->full_name }}
                                    <span style="opacity: 0.7;">({{ $prof->pivot->role }})</span>
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        @foreach($course->professors as $prof)
                        <form method="POST" action="{{ route('admin.assignments.unassign') }}" style="display: inline; margin-bottom: 0.25rem;">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <input type="hidden" name="professor_id" value="{{ $prof->id }}">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove {{ $prof->full_name }} from {{ $course->course_code }}?');" style="font-size: 0.7rem; padding: 2px 8px; margin-bottom: 2px;">
                                Remove {{ $prof->last_name }}
                            </button>
                        </form>
                        @endforeach
                    </td>
                </tr>
                @endif
            @endforeach
            @if($courses->where(fn($c) => $c->professors->count() > 0)->count() === 0)
            <tr>
                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">No course-professor assignments yet. Use the form above to assign professors to courses.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
