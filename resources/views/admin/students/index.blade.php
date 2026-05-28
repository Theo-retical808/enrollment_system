@extends('layouts.admin')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1>Student Management</h1>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Add, edit, and manage student accounts</p>
    </div>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">+ Add Student</a>
</div>

<div class="filter-bar" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.students.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, or email..." style="flex: 1; min-width: 200px; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border);">
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Students ({{ $students->total() }})</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>School</th>
                <th>Year Level</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td><code class="id-badge id-badge-student">{{ $student->student_id }}</code></td>
                <td><strong>{{ $student->full_name }}</strong></td>
                <td style="color: var(--admin-text-secondary);">{{ $student->email }}</td>
                <td>{{ $student->school->name ?? 'N/A' }}</td>
                <td>Year {{ $student->year_level }}</td>
                <td>
                    @if($student->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($student->status === 'inactive')
                        <span class="badge badge-warning">Inactive</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($student->status) }}</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Are you sure you want to delete this student?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">No students found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($students->hasPages())
<div class="pagination">
    {{ $students->appends(request()->query())->links() }}
</div>
@endif
@endsection
