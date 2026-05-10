@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1>Account Management</h1>
    <p>View all student, professor, and admin accounts</p>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.accounts') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; width: 100%;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, ID, or email..." style="flex: 1; min-width: 200px;">
        <select name="type">
            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Accounts</option>
            <option value="students" {{ $type === 'students' ? 'selected' : '' }}>Students</option>
            <option value="professors" {{ $type === 'professors' ? 'selected' : '' }}>Professors</option>
            <option value="admins" {{ $type === 'admins' ? 'selected' : '' }}>Admins</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

@if($admins->count() > 0)
<div class="card">
    <div class="card-header">
        <h2>Admins ({{ $admins->count() }})</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Admin ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($admins as $admin)
            <tr>
                <td><code style="background: #fef3c7; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">{{ $admin->admin_id }}</code></td>
                <td><strong>{{ $admin->full_name }}</strong></td>
                <td style="color: #64748b;">{{ $admin->email }}</td>
                <td>
                    @if($admin->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($admin->status) }}</span>
                    @endif
                </td>
                <td style="font-size: 0.8rem; color: #64748b;">{{ $admin->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($professors->count() > 0)
<div class="card">
    <div class="card-header">
        <h2>Professors ({{ $professors->count() }})</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Professor ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>School</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($professors as $professor)
            <tr>
                <td><code style="background: #dbeafe; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">{{ $professor->professor_id }}</code></td>
                <td><strong>{{ $professor->full_name }}</strong></td>
                <td style="color: #64748b;">{{ $professor->email }}</td>
                <td>{{ $professor->school->name ?? 'N/A' }}</td>
                <td>
                    @if($professor->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($professor->status) }}</span>
                    @endif
                </td>
                <td style="font-size: 0.8rem; color: #64748b;">{{ $professor->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($students->count() > 0)
<div class="card">
    <div class="card-header">
        <h2>Students ({{ $students->count() }})</h2>
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
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td><code style="background: #dcfce7; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">{{ $student->student_id }}</code></td>
                <td><strong>{{ $student->full_name }}</strong></td>
                <td style="color: #64748b;">{{ $student->email }}</td>
                <td>{{ $student->school->name ?? 'N/A' }}</td>
                <td>Year {{ $student->year_level }}</td>
                <td>
                    @if($student->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($student->status) }}</span>
                    @endif
                </td>
                <td style="font-size: 0.8rem; color: #64748b;">{{ $student->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($students->count() === 0 && $professors->count() === 0 && $admins->count() === 0)
<div class="card">
    <div class="card-body" style="text-align: center; padding: 3rem; color: #94a3b8;">
        <p>No accounts found matching your criteria.</p>
    </div>
</div>
@endif
@endsection
