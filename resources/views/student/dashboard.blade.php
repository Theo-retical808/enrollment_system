@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        /* Light Mode Colors */
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-light: #f1f5f9;
        
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

    /* Dark Mode Colors (Activates when Theo's toggle is flipped) */
    .dark, [data-theme="dark"], [data-bs-theme="dark"] {
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --border-light: #334155;
        
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

    /* Ensures the main .card class turns dark too */
    .dark .card, [data-theme="dark"] .card {
        background-color: var(--card-bg) !important;
        border-color: var(--card-border) !important;
        color: var(--text-main) !important;
    }

    /* LMS Specific Hero Banner Styles - Exact Match */
    .lms-banner {
        background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%); /* Slightly deeper blue */
        border-radius: 10px;
        padding: 2.25rem 3rem; /* Taller banner like LMS */
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        box-shadow: none;
    }
    
    .lms-banner-content h2 {
        font-size: 2.1rem; /* Larger, more prominent like LMS */
        font-weight: 700;
        margin-bottom: 0.35rem;
        letter-spacing: -0.02em;
        color: white;
    }
    
    .lms-banner-content p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1rem;
        font-weight: 400;
    }
    
    .lms-banner {
        background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%);
        border-radius: 12px;
        padding: 2.25rem 2.5rem; /* Precise 'stripe' height like LMS */
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        box-shadow: none;
    }
    
    .lms-banner-content h2 {
        font-size: 1.85rem; /* Perfectly scaled like LMS */
        font-weight: 700;
        margin-bottom: 0.25rem;
        letter-spacing: -0.01em;
        color: white;
    }
    
    .lms-banner-content p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        font-weight: 400;
    }
    
    .lms-role-badge {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 0.85rem 1.35rem; /* Proportional to the banner height */
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
        font-size: 1.15rem; /* Clean and readable like LMS */
        display: block;
        color: white;
        line-height: 1.1;
    }
</style>
<!-- Hero Banner -->
<div class="lms-banner">
    <div class="lms-banner-content">
        <h2>Welcome back, {{ strtoupper($student->first_name ?? $student->full_name) }}!</h2>
        <p>{{ now()->format('l, F j, Y') }}.</p>
    </div>
    <div class="lms-role-badge">
        <i data-lucide="contact-2" style="width: 22px; height: 22px; color: rgba(255,255,255,0.85);"></i>
        <div>
            <span class="lms-role-label">Current Role</span>
            <span class="lms-role-value">Student</span>
        </div>
    </div>
</div>

<style>
    .lms-stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 12px; /* Slightly tighter corners */
        padding: 1.1rem 1.25rem; /* Tightened padding */
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
</style>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.25rem;">
    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: #eff6ff; color: #3b82f6;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="lms-stat-content">
            <span class="label">Student ID</span>
            <h2 class="value">{{ $student->student_id }}</h2>
        </div>
    </div>

    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: #fdf4ff; color: #d946ef;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
        </div>
        <div class="lms-stat-content">
            <span class="label">Year Level</span>
            <h2 class="value">{{ $student->year_level }}</h2>
        </div>
    </div>

    <div class="lms-stat-card">
        <div class="lms-stat-icon" style="background: {{ $student->isRegular() ? '#f0fdf4' : '#fff7ed' }}; color: {{ $student->isRegular() ? '#22c55e' : '#f97316' }};">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
        <div class="lms-stat-content">
            <span class="label">Status</span>
            <h2 class="value">{{ $student->isRegular() ? 'Regular' : 'Irregular' }}</h2>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem;">
    <div class="card" style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
        <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.15rem;">Student Information</h3>
        <div style="display:flex; flex-direction:column; gap: 0.85rem;">
            <div style="display:flex; justify-content:space-between; border-bottom: 1px solid var(--border-light); padding-bottom: 0.5rem;">
                <span style="color: var(--text-muted);">Email</span>
                <span style="font-weight:600; color: var(--text-main);">{{ $student->email }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom: 1px solid var(--border-light); padding-bottom: 0.5rem;">
                <span style="color: var(--text-muted);">School</span>
                <span style="font-weight:600; color: var(--text-main);">{{ $student->school->name ?? 'N/A' }}</span>
            </div>
            <div style="background: var(--sky-bg); color: var(--sky-text); padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                {{ $classificationInfo['message'] }}
            </div>
        </div>
    </div>

    <div class="card" style="display:flex; flex-direction:column; justify-content:center; background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
        <h3 style="margin-bottom: 0.75rem; color: var(--text-primary); font-size: 1.15rem;">Payment Status</h3>
        @if($paymentStatus['status'] === 'paid')
            <div style="background: var(--success-bg); border-radius:12px; padding: 1.2rem; border-left: 4px solid var(--success-border);">
                <p style="color: var(--success-text); font-weight:700;">✓ Payment Completed</p>
                <p style="font-size:0.9rem; color: var(--success-text);">Amount: ₱{{ number_format($paymentStatus['amount_paid'], 2) }}</p>
            </div>
        @else
            <div style="background: var(--danger-bg); border-radius:12px; padding: 1.2rem; border-left: 4px solid var(--danger-border); margin-bottom:1rem;">
                <p style="color: var(--danger-text); font-weight:700;">Payment Required</p>
                <p style="font-size:0.85rem; color: var(--danger-text);">{{ $paymentStatus['message'] }}</p>
            </div>
            <a href="{{ route('student.payment.required') }}" style="background: var(--text-main); color: var(--card-bg); text-align:center; border-radius: 12px; padding: 0.8rem; text-decoration: none; font-weight: 700; display: block;" class="btn">Pay Now</a>
        @endif
    </div>
</div>

<div class="card" style="margin-top: 1.25rem; background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
    <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        Enrollment Status
    </h3>
    @if($currentEnrollment)
        <div style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--card-border);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                <div>
                    <span style="font-weight:700; color: var(--text-main);">{{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}</span>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Total Units: {{ $currentEnrollment->total_units }}</p>
                </div>
                <span style="padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; background: var(--sky-bg); color: var(--sky-text);">
                    {{ $currentEnrollment->status }}
                </span>
            </div>
            
            @if($currentEnrollment->courses->count() > 0)
                <table style="width:100%; text-align:left; border-collapse:collapse;">
                    <tr style="color: var(--text-muted); font-size:0.8rem; text-transform:uppercase;">
                        <th style="padding-bottom:10px; border-bottom: 1px solid var(--border-light);">Course</th>
                        <th style="padding-bottom:10px; border-bottom: 1px solid var(--border-light);">Schedule</th>
                    </tr>
                    @foreach($currentEnrollment->courses as $course)
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding: 12px 0;">
                            <span style="font-weight:700; color: var(--reg-text);">{{ $course->course_code }}</span><br>
                            <span style="font-size:0.85rem; color: var(--text-muted);">{{ $course->title }}</span>
                        </td>
                        <td style="font-size:0.85rem; padding: 12px 0;">
                            <span style="font-weight: 600; color: var(--text-main);">{{ $course->pivot->schedule_day ?? 'TBA' }}</span><br>
                            <span style="color: var(--text-muted);">{{ $course->pivot->room ?? 'No Room' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    @else
        <div style="text-align:center; padding: 2.5rem; background: var(--card-bg); border-radius: 12px; border: 1px dashed var(--text-muted);">
            <p style="color: var(--text-muted); margin-bottom:1.5rem; font-weight: 500;">No Current Enrollment Found</p>
            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" style="background: var(--reg-text); color: white; padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: background 0.3s;" class="btn">
                {{ $student->isRegular() ? 'Get My Schedule' : 'Start Enrollment' }}
            </a>
        </div>
    @endif
</div>
@endsection