@extends('layouts.professor')

@section('title', 'Professor Dashboard')

@section('content')

<div class="page-header mb-8 flex justify-between items-center">
    <div>
        <p class="text-muted font-bold" style="letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.8rem;">Academic Overview</p>
        <h1 class="text-main font-extrabold" style="font-size: 2.2rem;">Professor Dashboard</h1>
    </div>
    <div style="text-align: right;">
        <p class="text-muted font-bold" style="font-size: 0.85rem;">{{ now()->format('F d, Y') }}</p>
        <span class="badge badge-success">
            <i data-lucide="shield-check" style="width: 12px;"></i>
            Active Session
        </span>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
    <div style="background: var(--status-success-bg); border: 2px solid var(--status-success-text); color: var(--status-success-text); padding: 1.25rem; border-radius: var(--radius-lg); margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-sm);">
        <i data-lucide="check-circle-2" style="width: 24px; flex-shrink: 0; margin-top: 2px;"></i>
        <div style="font-weight: 700;">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div style="background: var(--status-danger-bg); border: 2px solid var(--status-danger-text); color: var(--status-danger-text); padding: 1.25rem; border-radius: var(--radius-lg); margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-sm);">
        <i data-lucide="alert-triangle" style="width: 24px; flex-shrink: 0; margin-top: 2px;"></i>
        <div style="font-weight: 700;">{{ session('error') }}</div>
    </div>
@endif

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-box" style="background: var(--udd-blue-light); color: var(--udd-blue);">
            <i data-lucide="clipboard-list" style="width: 28px; height: 28px;"></i>
        </div>
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Pending Reviews</span>
        <h2 class="text-main font-extrabold" style="font-size: 2.25rem; line-height: 1;">{{ $pendingEnrollments->count() }}</h2>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box" style="background: var(--status-success-bg); color: var(--status-success-text);">
            <i data-lucide="user-check" style="width: 28px; height: 28px;"></i>
        </div>
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Approved Today</span>
        <h2 class="text-main font-extrabold" style="font-size: 2.25rem; line-height: 1;">{{ $recentlyReviewed->where('status', 'approved')->count() }}</h2>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box" style="background: var(--status-danger-bg); color: var(--status-danger-text);">
            <i data-lucide="user-x" style="width: 28px; height: 28px;"></i>
        </div>
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Rejected Today</span>
        <h2 class="text-main font-extrabold" style="font-size: 2.25rem; line-height: 1;">{{ $recentlyReviewed->where('status', 'rejected')->count() }}</h2>
    </div>

    <div class="stat-card">
        <div class="stat-icon-box" style="background: var(--status-info-bg); color: var(--status-info-text);">
            <i data-lucide="users" style="width: 28px; height: 28px;"></i>
        </div>
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Total History</span>
        <h2 class="text-main font-extrabold" style="font-size: 2.25rem; line-height: 1;">{{ $recentlyReviewed->count() }}</h2>
    </div>
</div>

<div class="card mb-8" style="padding: 0; overflow: hidden;">
    <div class="flex justify-between items-center" style="padding: 1.5rem 2rem; border-bottom: 2px solid var(--border-light); background: var(--bg-primary);">
        <div class="flex items-center gap-3">
            <i data-lucide="clock" class="text-muted"></i>
            <h3 class="text-main font-extrabold" style="font-size: 1.25rem;">Pending Schedule Reviews</h3>
        </div>
        @if($pendingEnrollments->count() > 0)
            <span class="badge badge-danger" style="padding: 6px 14px; font-size: 0.85rem;">{{ $pendingEnrollments->count() }} ACTION REQUIRED</span>
        @endif
    </div>
    
    <div class="card-body" style="padding: 0;">
        @if($pendingEnrollments->isEmpty())
            <div style="padding: 5rem 2rem; text-align: center;">
                <div style="margin-bottom: 1.5rem; opacity: 0.3;">
                    <i data-lucide="coffee" style="width: 80px; height: 80px; color: var(--text-muted);"></i>
                </div>
                <h3 class="text-main font-extrabold" style="margin-bottom: 0.5rem;">All Caught Up!</h3>
                <p class="text-muted" style="font-weight: 600;">No pending schedules require your review at this moment.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--bg-primary); border-bottom: 2px solid var(--border-light);">
                            <th style="padding: 1.25rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Student Info</th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Classification</th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Course Load</th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Submission</th>
                            <th style="padding: 1.25rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingEnrollments as $enrollment)
                            <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" onmouseover="this.style.background='var(--border-light)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.5rem 2rem;">
                                    <div class="text-main font-extrabold" style="font-size: 1.05rem;">{{ $enrollment->student->full_name }}</div>
                                    <div class="text-muted font-bold" style="font-size: 0.85rem; margin-top: 2px;">ID: {{ $enrollment->student->student_id }}</div>
                                </td>
                                <td style="padding: 1.5rem 1rem;">
                                    @php
                                        $typeTheme = $enrollment->student->isRegular() ? 'info' : 'warning';
                                    @endphp
                                    <span class="badge badge-{{ $typeTheme }}" style="padding: 4px 12px; font-weight: 800;">{{ $enrollment->student->isRegular() ? 'REGULAR' : 'IRREGULAR' }}</span>
                                </td>
                                <td style="padding: 1.5rem 1rem;">
                                    <div class="text-main font-bold" style="font-size: 1rem;">{{ $enrollment->courses->count() }} Courses</div>
                                    <div class="text-muted font-bold" style="font-size: 0.85rem;">{{ $enrollment->total_units }} Units Total</div>
                                </td>
                                <td style="padding: 1.5rem 1rem;">
                                    <div class="flex items-center gap-2 text-muted font-bold" style="font-size: 0.85rem;">
                                        <i data-lucide="clock" style="width: 14px;"></i>
                                        {{ $enrollment->submitted_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td style="padding: 1.5rem 2rem; text-align: center;">
                                    <a href="{{ route('professor.review', $enrollment->id) }}" class="btn btn-primary" style="padding: 0.6rem 2rem; border-radius: 30px; font-size: 0.85rem;">
                                        Review Schedule
                                        <i data-lucide="chevron-right" style="width: 16px;"></i>
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

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 1.5rem 2rem; border-bottom: 2px solid var(--border-light);">
        <div class="flex items-center gap-3">
            <i data-lucide="history" class="text-muted"></i>
            <h3 class="text-main font-extrabold" style="font-size: 1.25rem;">Review History</h3>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($recentlyReviewed->isEmpty())
            <div style="padding: 3rem 2rem; text-align: center;">
                <p class="text-muted font-bold">Your recent activity will appear here.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--bg-primary); border-bottom: 1px solid var(--border-light);">
                            <th style="padding: 1rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Student</th>
                            <th style="padding: 1rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Outcome</th>
                            <th style="padding: 1rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Load</th>
                            <th style="padding: 1rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentlyReviewed as $enrollment)
                            <tr style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 1.25rem 2rem;">
                                    <div class="text-main font-bold">{{ $enrollment->student->full_name }}</div>
                                    <div class="text-muted font-bold" style="font-size: 0.75rem;">{{ $enrollment->student->student_id }}</div>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    @php
                                        $statusTheme = $enrollment->status === 'approved' ? 'success' : 'danger';
                                    @endphp
                                    <span class="badge badge-{{ $statusTheme }}" style="padding: 4px 12px; font-weight: 800; font-size: 0.7rem;">
                                        <i data-lucide="{{ $enrollment->status === 'approved' ? 'check' : 'x' }}" style="width: 10px;"></i>
                                        {{ strtoupper($enrollment->status) }}
                                    </span>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    <span class="text-main font-bold">{{ $enrollment->total_units }}</span> <span class="text-muted" style="font-size: 0.8rem;">Units</span>
                                </td>
                                <td style="padding: 1.25rem 2rem; text-align: right; color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">
                                    {{ $enrollment->reviewed_at->format('H:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection