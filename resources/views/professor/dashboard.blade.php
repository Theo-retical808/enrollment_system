@extends('layouts.professor')

@section('title', 'Professor Dashboard')

@section('content')

<style>
    :root {
        /* Dashboard-specific light mode colors */
        --sky-bg: #e0f2fe;
        --sky-border: #bae6fd;
        --sky-text: #0369a1;
        
        --blue-bg: #f0f9ff;
        --blue-border: #e0f2fe;
        --blue-text: #075985;
        
        --reg-bg: #eff6ff;
        --reg-border: #bfdbfe;
        --reg-text: #1d4ed8;
        
        --irreg-bg: #fffbeb;
        --irreg-border: #fef3c7;
        --irreg-text: #92400e;

        --success-bg: #dcfce7;
        --success-border: #059669;
        --success-text: #166534;
        
        --danger-bg: #fef2f2;
        --danger-border: #ef4444;
        --danger-text: #991b1b;
        
        --card-bg: #f8fafc;
        --card-border: #e2e8f0;
    }

    /* Dark Mode Colors */
    [data-theme="dark"] {
        --sky-bg: #0c4a6e;
        --sky-border: #075985;
        --sky-text: #bae6fd;
        
        --blue-bg: #1e3a8a;
        --blue-border: #1e40af;
        --blue-text: #bfdbfe;
        
        --reg-bg: #1e3a8a;
        --reg-border: #1e40af;
        --reg-text: #bfdbfe;
        
        --irreg-bg: #78350f;
        --irreg-border: #92400e;
        --irreg-text: #fde68a;

        --success-bg: #064e3b;
        --success-border: #059669;
        --success-text: #6ee7b7;
        
        --danger-bg: #7f1d1d;
        --danger-border: #ef4444;
        --danger-text: #fca5a5;
        
        --card-bg: #1e293b;
        --card-border: #334155;
    }

    /* Ensures cards turn dark */
    [data-theme="dark"] .card {
        background-color: var(--card-bg) !important;
        border-color: var(--card-border) !important;
        color: var(--text-main) !important;
    }

    /* LMS Stat Card */
    .lms-stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .lms-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lms-stat-content .label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.25rem;
        display: block;
    }
    .lms-stat-content .value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    [data-theme="dark"] .lms-stat-card {
        background: var(--bg-white) !important;
        border-color: var(--border-color) !important;
    }
    [data-theme="dark"] .lms-stat-content .label {
        color: var(--text-secondary) !important;
    }
    [data-theme="dark"] .lms-stat-content .value {
        color: var(--text-primary) !important;
    }

    /* Banner Styles */
    .lms-banner {
        background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%);
        border-radius: 12px;
        padding: 2.25rem 2.5rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        box-shadow: none;
    }
    
    .lms-banner-content h2 {
        font-size: 1.85rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        letter-spacing: -0.01em;
        color: white;
    }
    
    .lms-banner-content p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        font-weight: 400;
        margin: 0;
    }
    
    .lms-role-badge {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 0.85rem 1.35rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    
    .lms-role-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: 2px;
        display: block;
        font-weight: 600;
    }
    
    .lms-role-value {
        font-weight: 700;
        font-size: 1.15rem;
        display: block;
        color: white;
        line-height: 1.1;
    }
</style>

<!-- Hero Banner -->
<div class="lms-banner">
    <div class="lms-banner-content">
        <h2>Welcome back, {{ strtoupper(Auth::guard('professor')->user()->first_name ?? Auth::guard('professor')->user()->full_name) }}!</h2>
        <p>{{ now()->format('l, F j, Y') }}.</p>
    </div>
    <div class="lms-role-badge">
        <i data-lucide="contact-2" style="width: 22px; height: 22px; color: rgba(255,255,255,0.85);"></i>
        <div>
            <span class="lms-role-label">Current Role</span>
            <span class="lms-role-value">Professor</span>
        </div>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="flash-message flash-success" style="margin-bottom: 2rem;">
        <i data-lucide="check-circle-2" style="width: 24px; flex-shrink: 0; margin-top: 2px;"></i>
        <div style="font-weight: 700;">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="flash-message flash-error" style="margin-bottom: 2rem;">
        <i data-lucide="alert-triangle" style="width: 24px; flex-shrink: 0; margin-top: 2px;"></i>
        <div style="font-weight: 700;">{{ session('error') }}</div>
    </div>
@endif

<!-- Stats Grid -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem;">
    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: #eff6ff; color: #3b82f6;">
            <i data-lucide="clipboard-list" style="width: 24px; height: 24px;"></i>
        </div>
        <div class="lms-stat-content">
            <span class="label">Pending Reviews</span>
            <h2 class="value">{{ $pendingEnrollments->count() }}</h2>
        </div>
    </div>

    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: #f0fdf4; color: #22c55e;">
            <i data-lucide="user-check" style="width: 24px; height: 24px;"></i>
        </div>
        <div class="lms-stat-content">
            <span class="label">Approved Today</span>
            <h2 class="value">{{ $recentlyReviewed->where('status', 'approved')->count() }}</h2>
        </div>
    </div>

    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: #fef2f2; color: #ef4444;">
            <i data-lucide="user-x" style="width: 24px; height: 24px;"></i>
        </div>
        <div class="lms-stat-content">
            <span class="label">Rejected Today</span>
            <h2 class="value">{{ $recentlyReviewed->where('status', 'rejected')->count() }}</h2>
        </div>
    </div>

    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: #fdf4ff; color: #d946ef;">
            <i data-lucide="users" style="width: 24px; height: 24px;"></i>
        </div>
        <div class="lms-stat-content">
            <span class="label">Total History</span>
            <h2 class="value">{{ $recentlyReviewed->count() }}</h2>
        </div>
    </div>
</div>

<div class="card mb-5" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
    <div class="flex justify-between items-center" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-light); background: var(--card-bg);">
        <div class="flex items-center gap-2">
            <i data-lucide="clock" class="text-muted" style="width: 18px;"></i>
            <h3 style="color: var(--text-primary); font-size: 1.15rem; margin: 0; font-weight: 700;">Pending Schedule Reviews</h3>
        </div>
        @if($pendingEnrollments->count() > 0)
            <span style="background: var(--danger-bg); color: var(--danger-text); padding: 4px 10px; font-size: 0.7rem; font-weight: 800; border-radius: var(--radius-full);">{{ $pendingEnrollments->count() }} ACTION REQUIRED</span>
        @endif
    </div>
    
    <div style="padding: 0;">
        @if($pendingEnrollments->isEmpty())
            <div style="padding: 5rem 2rem; text-align: center;">
                <div style="margin-bottom: 1.5rem; opacity: 0.3;">
                    <i data-lucide="coffee" style="width: 80px; height: 80px; color: var(--text-muted);"></i>
                </div>
                <h3 class="text-main font-extrabold" style="margin-bottom: 0.5rem; font-size: 1.2rem;">All Caught Up!</h3>
                <p class="text-muted" style="font-weight: 600; font-size: 0.95rem;">No pending schedules require your review at this moment.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--card-bg); border-bottom: 1px solid var(--border-light);">
                            <th style="padding: 0.75rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Student Info</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Classification</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Course Load</th>
                            <th style="padding: 0.75rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Submission</th>
                            <th style="padding: 0.75rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center;">Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingEnrollments as $enrollment)
                            <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" onmouseover="this.style.background='var(--card-bg)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.5rem;">
                                    <div class="text-main font-extrabold" style="font-size: 0.95rem;">{{ $enrollment->student->full_name }}</div>
                                    <div class="text-muted font-bold" style="font-size: 0.75rem; margin-top: 2px;">{{ $enrollment->student->student_id }}</div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; background: {{ $enrollment->student->isRegular() ? 'var(--sky-bg)' : 'var(--irreg-bg)' }}; color: {{ $enrollment->student->isRegular() ? 'var(--sky-text)' : 'var(--irreg-text)' }};">
                                        {{ $enrollment->student->isRegular() ? 'REGULAR' : 'IRREGULAR' }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div class="text-main font-bold" style="font-size: 0.9rem;">{{ $enrollment->courses->count() }} Courses</div>
                                    <div class="text-muted font-bold" style="font-size: 0.75rem;">{{ $enrollment->total_units }} Units</div>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <div class="flex items-center gap-2 text-muted font-bold" style="font-size: 0.75rem;">
                                        <i data-lucide="clock" style="width: 14px;"></i>
                                        {{ $enrollment->submitted_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: center;">
                                    <a href="{{ route('professor.review', $enrollment->id) }}" class="btn btn-primary" style="padding: 0.5rem 1.25rem; border-radius: 20px; font-size: 0.8rem; background: #3b82f6; color: white; border: none; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);">
                                        Review
                                        <i data-lucide="chevron-right" style="width: 14px; margin-left: 4px;"></i>
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

<div class="card" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-light); background: var(--card-bg);">
        <div class="flex items-center gap-2">
            <i data-lucide="history" class="text-muted" style="width: 18px;"></i>
            <h3 style="color: var(--text-primary); font-size: 1.15rem; margin: 0; font-weight: 700;">Review History</h3>
        </div>
    </div>
    <div style="padding: 0;">
        @if($recentlyReviewed->isEmpty())
            <div style="padding: 3rem 2rem; text-align: center;">
                <p class="text-muted font-bold" style="font-size: 0.95rem;">Your recent activity will appear here.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--card-bg); border-bottom: 1px solid var(--border-light);">
                            <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Student</th>
                            <th style="padding: 1rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Outcome</th>
                            <th style="padding: 1rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Load</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentlyReviewed as $enrollment)
                            <tr style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 1.25rem 1.5rem;">
                                    <div class="text-main font-bold" style="font-size: 0.95rem;">{{ $enrollment->student->full_name }}</div>
                                    <div class="text-muted font-bold" style="font-size: 0.75rem; margin-top: 2px;">{{ $enrollment->student->student_id }}</div>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    @php
                                        $statusTheme = $enrollment->status === 'approved' ? 'success' : 'danger';
                                    @endphp
                                    <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; background: var(--{{ $statusTheme }}-bg); color: var(--{{ $statusTheme }}-text); display: inline-flex; align-items: center; gap: 4px;">
                                        <i data-lucide="{{ $enrollment->status === 'approved' ? 'check' : 'x' }}" style="width: 12px; height: 12px;"></i>
                                        {{ strtoupper($enrollment->status) }}
                                    </span>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    <span class="text-main font-bold" style="font-size: 0.9rem;">{{ $enrollment->total_units }}</span> <span class="text-muted" style="font-size: 0.8rem;">Units</span>
                                </td>
                                <td style="padding: 1.25rem 1.5rem; text-align: right; color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">
                                    {{ $enrollment->reviewed_at->format('h:i A') }}
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