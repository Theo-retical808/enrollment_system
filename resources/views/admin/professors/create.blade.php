@extends('layouts.admin')

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1>Add New Professor</h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Create a new professor account</p>
</div>

<div class="card">
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.professors.store') }}">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Professor ID *</label>
                    <input type="text" name="professor_id" value="{{ old('professor_id') }}" required placeholder="e.g., PROF010" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('professor_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="professor@university.edu" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('email') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('first_name') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('last_name') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Password *</label>
                    <input type="password" name="password" required minlength="6" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                    @error('password') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">School *</label>
                    <select name="school_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="can_assist_enrollment" value="1" {{ old('can_assist_enrollment') ? 'checked' : '' }}>
                    <span style="font-size: 0.85rem; color: var(--admin-text-primary);">Designate as Enrollment Assistant</span>
                </label>
                <p style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.25rem; margin-left: 1.5rem;">This professor can assist in the enrollment review process.</p>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Create Professor</button>
                <a href="{{ route('admin.professors.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
