@extends('layouts.admin')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1>Course Management</h1>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Add, edit, and manage courses</p>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">+ Add Course</a>
</div>

<div class="filter-bar" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.courses.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or title..." style="flex: 1; min-width: 200px; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border);">
        <select name="school_id" style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border);">
            <option value="">All Schools</option>
            @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request('search') || request('school_id'))
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Courses ({{ $courses->total() }})</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Units</th>
                <th>School</th>
                <th>Year / Sem</th>
                <th>Professors</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $course)
            <tr>
                <td><code class="id-badge id-badge-course">{{ $course->course_code }}</code></td>
                <td><strong>{{ $course->title }}</strong></td>
                <td>{{ $course->units }}</td>
                <td>{{ $course->school->name ?? 'N/A' }}</td>
                <td style="font-size: 0.8rem;">
                    @if($course->year_level)
                        Y{{ $course->year_level }}
                        @if($course->semester) / S{{ $course->semester }} @endif
                    @else
                        <span style="color: var(--admin-text-secondary);">—</span>
                    @endif
                </td>
                <td>
                    @if($course->professors->count() > 0)
                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                            @foreach($course->professors->take(2) as $prof)
                                <span class="pill-tag">{{ $prof->last_name }}</span>
                            @endforeach
                            @if($course->professors->count() > 2)
                                <span style="font-size: 0.7rem; color: var(--admin-text-secondary);">+{{ $course->professors->count() - 2 }}</span>
                            @endif
                        </div>
                    @else
                        <span style="color: var(--admin-text-secondary); font-size: 0.8rem;">None</span>
                    @endif
                </td>
                <td>
                    @if($course->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">No courses found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($courses->hasPages())
<div class="pagination">
    {{ $courses->appends(request()->query())->links() }}
</div>
@endif
@endsection
