@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1>Enrollment Management</h1>
    <p>View all enrollments and override professor decisions when needed</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Enrollments</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Review</div>
        <div class="stat-value" style="color: #f59e0b;">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved</div>
        <div class="stat-value" style="color: #16a34a;">{{ $stats['approved'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rejected</div>
        <div class="stat-value" style="color: #ef4444;">{{ $stats['rejected'] }}</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.enrollments') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; width: 100%;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student name or ID..." style="flex: 1; min-width: 200px;">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>School</th>
                <th>Courses</th>
                <th>Status</th>
                <th>Comments</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $enrollment)
            <tr>
                <td>
                    <strong>{{ $enrollment->student->full_name ?? 'N/A' }}</strong><br>
                    <span style="color: #64748b; font-size: 0.75rem;">{{ $enrollment->student->student_id ?? '' }}</span>
                </td>
                <td style="font-size: 0.8rem;">{{ $enrollment->student->school->name ?? 'N/A' }}</td>
                <td style="font-size: 0.8rem;">{{ $enrollment->courses->count() }} course(s)</td>
                <td>
                    @if($enrollment->status === 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($enrollment->status === 'submitted')
                        <span class="badge badge-warning">Pending</span>
                    @elseif($enrollment->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @elseif($enrollment->status === 'draft')
                        <span class="badge badge-secondary">Draft</span>
                    @else
                        <span class="badge badge-secondary">{{ ucfirst($enrollment->status) }}</span>
                    @endif
                </td>
                <td style="font-size: 0.8rem; color: #64748b; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $enrollment->review_comments ?? '—' }}
                </td>
                <td style="font-size: 0.8rem; color: #64748b;">{{ $enrollment->created_at->format('M d, Y') }}</td>
                <td>
                    @if($enrollment->status !== 'draft')
                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                        @if($enrollment->status !== 'approved')
                        <form method="POST" action="{{ route('admin.enrollments.override', $enrollment->id) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="admin_comments" value="Approved by admin">
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Override and APPROVE this enrollment?')">Approve</button>
                        </form>
                        @endif
                        @if($enrollment->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.enrollments.override', $enrollment->id) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="admin_comments" value="Rejected by admin">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Override and REJECT this enrollment?')">Reject</button>
                        </form>
                        @endif
                    </div>
                    @else
                        <span style="color: #94a3b8; font-size: 0.8rem;">Draft</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">No enrollments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($enrollments->hasPages())
<div class="pagination">
    {{ $enrollments->appends(request()->query())->links('pagination::simple-default') }}
</div>
@endif
@endsection
