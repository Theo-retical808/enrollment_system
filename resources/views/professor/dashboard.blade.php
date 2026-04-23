@extends('layouts.professor')

@section('title', 'Professor Dashboard')

@section('content')

<style>
    :root {
        /* General Colors */
        --text-main: #0f172a;
        --text-muted: #64748b;
        --text-light: #475569;
        --bg-main: transparent;
        --border-light: #f1f5f9;
        
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --table-header-bg: #f8fafc;
        
        /* Primary Theme (Blue) */
        --primary-bg: #eff6ff;
        --primary-text: #2563eb;
        --primary-bg-solid: #2563eb;
        
        /* Success Theme (Approved) */
        --success-bg-light: #f0fdf4;
        --success-bg: #dcfce7;
        --success-border: #dcfce7;
        --success-text: #166534;
        --success-icon: #16a34a;
        
        /* Danger Theme (Rejected) */
        --danger-bg-light: #fef2f2;
        --danger-bg: #fee2e2;
        --danger-border: #fee2e2;
        --danger-text: #991b1b;
        --danger-icon: #ef4444;

        /* Warning Theme (Irregular Student) */
        --warning-bg: #fff7ed;
        --warning-text: #ea580c;

        /* Neutral Theme (Total Reviewed) */
        --neutral-bg: #f1f5f9;
        --neutral-text: #475569;
    }

    /* Dark Mode Colors - Sleek Slate */
    .dark, [data-theme="dark"], [data-bs-theme="dark"] {
        /* General Colors */
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --text-light: #cbd5e1;
        --bg-main: transparent;
        --border-light: #334155;
        
        --card-bg: #1e293b;
        --card-border: #334155;
        --table-header-bg: #0f172a;
        
        /* Primary Theme */
        --primary-bg: #1e3a8a;
        --primary-text: #60a5fa;
        --primary-bg-solid: #3b82f6;
        
        /* Success Theme */
        --success-bg-light: #022c22;
        --success-bg: #064e3b;
        --success-border: #065f46;
        --success-text: #6ee7b7;
        --success-icon: #22c55e;
        
        /* Danger Theme */
        --danger-bg-light: #450a0a;
        --danger-bg: #7f1d1d;
        --danger-border: #991b1b;
        --danger-text: #fca5a5;
        --danger-icon: #f87171;

        /* Warning Theme */
        --warning-bg: #7c2d12;
        --warning-text: #fdba74;

        /* Neutral Theme */
        --neutral-bg: #0f172a;
        --neutral-text: #94a3b8;
    }
</style>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">Dashboard</h1>
    <p class="page-subtitle" style="color: var(--text-muted); font-size: 0.95rem;">Review and manage student enrollment schedules</p>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background: var(--success-bg-light); border: 1px solid var(--success-border); color: var(--success-text); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" style="background: var(--danger-bg-light); border: 1px solid var(--danger-border); color: var(--danger-text); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20" width="20" height="20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>{{ session('error') }}</div>
    </div>
@endif

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div class="stat-icon" style="background: var(--primary-bg); color: var(--primary-text); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
        </div>
        <div class="stat-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Pending Reviews</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 800; color: var(--text-main);">{{ $pendingEnrollments->count() }}</div>
    </div>

    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div class="stat-icon" style="background: var(--success-bg); color: var(--success-icon); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Approved Today</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 800; color: var(--text-main);">{{ $recentlyReviewed->where('status', 'approved')->count() }}</div>
    </div>

    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger-icon); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Rejected Today</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 800; color: var(--text-main);">{{ $recentlyReviewed->where('status', 'rejected')->count() }}</div>
    </div>

    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div class="stat-icon" style="background: var(--neutral-bg); color: var(--neutral-text); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
        </div>
        <div class="stat-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total Reviewed</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 800; color: var(--text-main);">{{ $recentlyReviewed->count() }}</div>
    </div>
</div>

<div class="card" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); margin-bottom: 2rem; overflow: hidden;">
    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between;">
        <h2 class="card-title" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <svg width="20" height="20" fill="var(--primary-bg-solid)" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            Pending Schedule Reviews
            @if($pendingEnrollments->count() > 0)
                <span class="badge" style="background: var(--danger-bg); color: var(--danger-icon); padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; margin-left: 0.5rem;">{{ $pendingEnrollments->count() }}</span>
            @endif
        </h2>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($pendingEnrollments->isEmpty())
            <div class="empty-state" style="padding: 4rem 2rem; text-align: center;">
                <svg class="empty-state-icon" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div class="empty-state-title" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem;">No pending reviews</div>
                <div class="empty-state-text" style="color: var(--text-muted); font-size: 0.9rem;">All schedules have been reviewed. Check back later for new submissions.</div>
            </div>
        @else
            <div class="table-container" style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: var(--table-header-bg); border-bottom: 1px solid var(--border-light);">
                        <tr>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Student</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Student ID</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Year Level</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Type</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Courses</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total Units</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Submitted</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingEnrollments as $enrollment)
                            <tr style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $enrollment->student->full_name }}</div>
                                </td>
                                <td style="padding: 1rem 1.5rem;">
                                    <span style="font-weight: 500; color: var(--text-light);">{{ $enrollment->student->student_id }}</span>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: var(--text-light);">Year {{ $enrollment->student->year_level }}</td>
                                <td style="padding: 1rem 1.5rem;">
                                    @if($enrollment->student->isRegular())
                                        <span class="badge" style="background: var(--primary-bg); color: var(--primary-text); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Regular</span>
                                    @else
                                        <span class="badge" style="background: var(--warning-bg); color: var(--warning-text); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Irregular</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; color: var(--text-light);">{{ $enrollment->courses->count() }}</td>
                                <td style="padding: 1rem 1.5rem;">
                                    <span style="font-weight: 600; color: var(--text-main);">{{ $enrollment->total_units }}</span> <span style="color: var(--text-muted);">units</span>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.9rem;">{{ $enrollment->submitted_at->diffForHumans() }}</td>
                                <td style="padding: 1rem 1.5rem;">
                                    <a href="{{ route('professor.review', $enrollment->id) }}" class="btn" style="background: var(--primary-bg-solid); color: white; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s;">
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

<div class="card" style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden;">
    <div class="card-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
        <h2 class="card-title" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
            <svg width="20" height="20" fill="var(--text-muted)" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            Recently Reviewed
        </h2>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($recentlyReviewed->isEmpty())
            <div class="empty-state" style="padding: 4rem 2rem; text-align: center;">
                <svg class="empty-state-icon" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="empty-state-title" style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem;">No recent reviews</div>
                <div class="empty-state-text" style="color: var(--text-muted); font-size: 0.9rem;">Your review history will appear here.</div>
            </div>
        @else
            <div class="table-container" style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: var(--table-header-bg); border-bottom: 1px solid var(--border-light);">
                        <tr>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Student</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Status</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total Units</th>
                            <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentlyReviewed as $enrollment)
                            <tr style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 1rem 1.5rem;">
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $enrollment->student->full_name }}</div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">{{ $enrollment->student->student_id }}</div>
                                </td>
                                <td style="padding: 1rem 1.5rem;">
                                    @if($enrollment->status === 'approved')
                                        <span class="badge" style="background: var(--success-bg); color: var(--success-text); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Approved</span>
                                    @else
                                        <span class="badge" style="background: var(--danger-bg); color: var(--danger-text); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Rejected</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; color: var(--text-light);">
                                    <span style="font-weight: 600; color: var(--text-main);">{{ $enrollment->total_units }}</span> units
                                </td>
                                <td style="padding: 1rem 1.5rem; color: var(--text-muted); font-size: 0.9rem;">{{ $enrollment->reviewed_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection