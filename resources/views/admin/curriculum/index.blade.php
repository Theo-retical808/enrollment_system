@extends('layouts.admin')

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1>Curriculum Templates</h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Define default schedules for regular students per year level and semester</p>
</div>

<!-- School Selector -->
<div class="filter-bar" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.curriculum.index') }}" style="display: flex; gap: 0.75rem; align-items: center;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--admin-text-primary);">School:</label>
        <select name="school_id" onchange="this.form.submit()" style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary);">
            @foreach($schools as $school)
                <option value="{{ $school->id }}" {{ $selectedSchool && $selectedSchool->id == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Add to Curriculum Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2>Add Course to Curriculum</h2>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.curriculum.store') }}">
            @csrf
            <input type="hidden" name="school_id" value="{{ $selectedSchool->id ?? '' }}">
            <div style="display: grid; grid-template-columns: 1fr 1fr 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Year Level *</label>
                    <select name="year_level" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}">Year {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Semester *</label>
                    <select name="semester" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Course *</label>
                    <select name="course_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->title }} ({{ $course->units }}u)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Schedule (Optional)</label>
                    <select name="course_schedule_id" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        <option value="">No specific schedule</option>
                        @foreach($schedules as $sched)
                            <option value="{{ $sched->id }}">{{ $sched->course->course_code }} - {{ $sched->professor->full_name }} ({{ $sched->day }} {{ $sched->time_range }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add to Curriculum</button>
        </form>
    </div>
</div>

<!-- Current Curriculum -->
@if($templates->count() > 0)
    @foreach($templates as $groupName => $items)
    <div class="card" style="margin-bottom: 1rem;">
        <div class="card-header">
            <h2>{{ $groupName }}</h2>
            <span style="font-size: 0.8rem; color: var(--admin-text-secondary);">{{ $items->sum(fn($t) => $t->course->units) }} total units</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Units</th>
                    <th>Assigned Schedule</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $template)
                <tr>
                    <td><code class="id-badge id-badge-course">{{ $template->course->course_code }}</code></td>
                    <td>{{ $template->course->title }}</td>
                    <td>{{ $template->course->units }}</td>
                    <td style="font-size: 0.8rem;">
                        @if($template->courseSchedule)
                            {{ $template->courseSchedule->professor->full_name }} — {{ $template->courseSchedule->day }} {{ $template->courseSchedule->time_range }} ({{ $template->courseSchedule->room }})
                        @else
                            <span style="color: var(--admin-text-secondary);">Not assigned</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.curriculum.destroy', $template) }}" onsubmit="return confirm('Remove this course from curriculum?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
@else
<div class="card">
    <div style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">
        <p>No curriculum template defined for {{ $selectedSchool->name ?? 'this school' }} yet.</p>
        <p style="font-size: 0.8rem;">Use the form above to add courses to the default regular schedule.</p>
    </div>
</div>
@endif
@endsection
