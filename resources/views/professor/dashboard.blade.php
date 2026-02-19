@extends('layouts.professor')

@section('title', 'Professor Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Review and manage student enrollment schedules</p>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="alert alert-success">
        <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>{{ session('error') }}</div>
    </div>
@endif

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
        </div>
        <div class="stat-label">Pending Reviews</div>
        <div class="stat-value">{{ $pendingEnrollments->count() }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #dcfce7; color: #10b981;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label">Approved Today</div>
        <div class="stat-value">{{ $recentlyReviewed->where('status', 'approved')->count() }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #fee2e2; color: #ef4444;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label">Rejected Today</div>
        <div class="stat-value">{{ $recentlyReviewed->where('status', 'rejected')->count() }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
        </div>
        <div class="stat-label">Total Reviewed</div>
        <div class="stat-value">{{ $recentlyReviewed->count() }}</div>
    </div>
</div>

<!-- Pending Reviews -->
<div class="card mb-8">
    <div class="card-header">
        <h2 class="card-title">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            Pending Schedule Reviews
            @if($pendingEnrollments->count() > 0)
                <span class="badge badge-danger">{{ $pendingEnrollments->count() }}</span>
            @endif
        </h2>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($pendingEnrollments->isEmpty())
            <div class="empty-state">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div class="empty-state-title">No pending reviews</div>
                <div class="empty-state-text">All schedules have been reviewed. Check back later for new submissions.</div>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Year Level</th>
                            <th>Type</th>
                            <th>Courses</th>
                            <th>Total Units</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingEnrollments as $enrollment)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $enrollment->student->full_name }}</div>
                                </td>
                                <td>
                                    <span class="font-medium">{{ $enrollment->student->student_id }}</span>
                                </td>
                                <td>Year {{ $enrollment->student->year_level }}</td>
                                <td>
                                    @if($enrollment->student->isRegular())
                                        <span class="badge badge-success">Regular</span>
                                    @else
                                        <span class="badge badge-warning">Irregular</span>
                                    @endif
                                </td>
                                <td>{{ $enrollment->courses->count() }}</td>
                                <td>
                                    <span class="font-semibold">{{ $enrollment->total_units }}</span> units
                                </td>
                                <td>{{ $enrollment->submitted_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('professor.review', $enrollment->id) }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Recently Reviewed -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            Recently Reviewed
        </h2>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($recentlyReviewed->isEmpty())
            <div class="empty-state">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="empty-state-title">No recent reviews</div>
                <div class="empty-state-text">Your review history will appear here.</div>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Total Units</th>
                            <th>Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentlyReviewed as $enrollment)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-900">{{ $enrollment->student->full_name }}</div>
                                    <div class="text-sm text-gray-600">{{ $enrollment->student->student_id }}</div>
                                </td>
                                <td>
                                    @if($enrollment->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $enrollment->total_units }} units</td>
                                <td>{{ $enrollment->reviewed_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection