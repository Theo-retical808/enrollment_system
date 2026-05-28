@extends('layouts.admin')

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1>Edit Course: {{ $course->course_code }}</h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">{{ $course->title }}</p>
</div>

<div class="card">
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.courses.update', $course) }}">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Course Code *</label>
                    <input type="text" name="course_code" value="{{ old('course_code', $course->course_code) }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('course_code') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $course->title) }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('title') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Description</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); resize: vertical;">{{ old('description', $course->description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Units *</label>
                    <input type="number" name="units" value="{{ old('units', $course->units) }}" required min="1" max="6" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">School *</label>
                    <select name="school_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id', $course->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Year Level</label>
                    <select name="year_level" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        <option value="">Any</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('year_level', $course->year_level) == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Semester</label>
                    <select name="semester" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        <option value="">Any</option>
                        <option value="1" {{ old('semester', $course->semester) === '1' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2" {{ old('semester', $course->semester) === '2' ? 'selected' : '' }}>2nd Semester</option>
                        <option value="summer" {{ old('semester', $course->semester) === 'summer' ? 'selected' : '' }}>Summer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Status</label>
                    <select name="is_active" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        <option value="1" {{ old('is_active', $course->is_active) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !old('is_active', $course->is_active) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Prerequisites</label>
                <p style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-bottom: 0.5rem;">Hold Ctrl/Cmd to select multiple courses</p>
                <select name="prerequisites[]" multiple style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); min-height: 120px;">
                    @foreach($allCourses as $c)
                        <option value="{{ $c->id }}" {{ in_array($c->id, old('prerequisites', $course->prerequisites->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $c->course_code }} - {{ $c->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($course->professors->count() > 0)
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: var(--admin-table-header-bg); border-radius: 6px;">
                <h3 style="font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--admin-text-primary);">Assigned Professors</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($course->professors as $prof)
                        <span class="pill-tag">
                            {{ $prof->full_name }} ({{ $prof->pivot->role }})
                        </span>
                    @endforeach
                </div>
                <p style="font-size: 0.7rem; color: var(--admin-text-secondary); margin-top: 0.5rem;">Manage assignments from the <a href="{{ route('admin.assignments.index') }}" style="color: var(--udd-blue);">Assignments</a> page.</p>
            </div>
            @endif

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Update Course</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
