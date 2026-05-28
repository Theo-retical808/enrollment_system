@extends('layouts.admin')

@section('content')
<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1>Schedule Management</h1>
    <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Assign class schedules to professors (subject, professor, day, time, room)</p>
</div>

<!-- Add Schedule Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2>Add New Schedule</h2>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.schedules.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Subject *</label>
                    <select name="course_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->course_code }} - {{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Professor *</label>
                    <select name="professor_id" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        <option value="">Select Professor</option>
                        @foreach($professors as $professor)
                            <option value="{{ $professor->id }}" {{ old('professor_id') == $professor->id ? 'selected' : '' }}>{{ $professor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Day *</label>
                    <select name="day" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                        <option value="">Select Day</option>
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                            <option value="{{ $day }}" {{ old('day') === $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Start Time *</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">End Time *</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Room *</label>
                    <input type="text" name="room" value="{{ old('room') }}" required placeholder="e.g., Room 201" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Max Students</label>
                    <input type="number" name="max_students" value="{{ old('max_students', 40) }}" min="1" max="100" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Schedule</button>
        </form>
    </div>
</div>

<!-- Current Schedules -->
<div class="card">
    <div class="card-header">
        <h2>All Schedules ({{ $schedules->count() }})</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Professor</th>
                <th>Day</th>
                <th>Time</th>
                <th>Room</th>
                <th>Slots</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
            <tr>
                <td>
                    <code class="id-badge id-badge-course">{{ $schedule->course->course_code }}</code>
                    <span style="margin-left: 0.5rem; font-size: 0.8rem;">{{ $schedule->course->title }}</span>
                </td>
                <td><strong>{{ $schedule->professor->full_name }}</strong></td>
                <td>{{ $schedule->day }}</td>
                <td style="font-size: 0.85rem;">{{ $schedule->time_range }}</td>
                <td><code style="font-size: 0.8rem;">{{ $schedule->room }}</code></td>
                <td style="font-size: 0.8rem;">{{ $schedule->enrolled_count }}/{{ $schedule->max_students }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Delete this schedule?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">No schedules created yet. Use the form above to add schedules.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
