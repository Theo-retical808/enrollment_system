@extends('layouts.admin')

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1>Edit Professor: {{ $professor->full_name }}</h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Update professor account details</p>
</div>

<div class="card">
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.professors.update', $professor) }}">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Professor ID *</label>
                    <input type="text" name="professor_id" value="{{ old('professor_id', $professor->professor_id) }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('professor_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $professor->email) }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('email') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $professor->first_name) }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('first_name') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $professor->last_name) }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('last_name') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Password <span style="font-weight: 400; color: var(--admin-text-secondary);">(leave blank to keep current)</span></label>
                    <input type="password" name="password" minlength="6" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('password') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">School *</label>
                    <select name="school_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id', $professor->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Status *</label>
                    <select name="status" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        <option value="active" {{ old('status', $professor->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $professor->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $professor->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="can_assist_enrollment" value="1" {{ old('can_assist_enrollment', $professor->can_assist_enrollment) ? 'checked' : '' }}>
                        <span style="font-size: 0.85rem; color: var(--admin-text-primary);">Enrollment Assistant</span>
                    </label>
                </div>
            </div>

            @if($professor->courses->count() > 0)
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: var(--admin-table-header-bg); border-radius: 6px;">
                <h3 style="font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--admin-text-primary);">Assigned Courses</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($professor->courses as $course)
                        <span class="pill-tag">
                            {{ $course->course_code }} ({{ $course->pivot->role }})
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Update Professor</button>
                <a href="{{ route('admin.professors.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
