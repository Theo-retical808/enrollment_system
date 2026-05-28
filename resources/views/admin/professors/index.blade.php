@extends('layouts.admin')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1>Professor Management</h1>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Add, edit, and manage professor accounts</p>
    </div>
    <a href="{{ route('admin.professors.create') }}" class="btn btn-primary">+ Add Professor</a>
</div>

<div class="filter-bar" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('admin.professors.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID, or email..." style="flex: 1; min-width: 200px; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border);">
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.professors.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Professors ({{ $professors->total() }})</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Professor ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>School</th>
                <th>Status</th>
                <th>Enrollment Assistant</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($professors as $professor)
            <tr>
                <td><code class="id-badge id-badge-professor">{{ $professor->professor_id }}</code></td>
                <td><strong>{{ $professor->full_name }}</strong></td>
                <td style="color: var(--admin-text-secondary);">{{ $professor->email }}</td>
                <td>{{ $professor->school->name ?? 'N/A' }}</td>
                <td>
                    @if($professor->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($professor->status === 'inactive')
                        <span class="badge badge-warning">Inactive</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($professor->status) }}</span>
                    @endif
                </td>
                <td>
                    @if($professor->can_assist_enrollment)
                        <span class="badge badge-success">Yes</span>
                    @else
                        <span style="color: var(--admin-text-secondary); font-size: 0.8rem;">No</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.professors.edit', $professor) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.professors.destroy', $professor) }}" onsubmit="return confirm('Are you sure you want to delete this professor?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">No professors found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($professors->hasPages())
<div class="pagination">
    {{ $professors->appends(request()->query())->links() }}
</div>
@endif
@endsection
