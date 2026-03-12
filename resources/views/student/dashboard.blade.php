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
</style>

<div style="margin-bottom: 2rem;">
    <p style="color: var(--text-muted); font-weight: 500;">Welcome back, {{ $student->full_name }}</p>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: var(--sky-bg); padding: 1.5rem; border-radius: 20px; border: 1px solid var(--sky-border);">
        <span style="color: var(--sky-text); font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Student ID</span>
        <h2 style="color: var(--sky-text); font-size: 1.8rem; margin-top: 5px;">{{ $student->student_id }}</h2>
    </div>
    
    <div style="background: var(--blue-bg); padding: 1.5rem; border-radius: 20px; border: 1px solid var(--blue-border);">
        <span style="color: var(--blue-text); font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Year Level</span>
        <h2 style="color: var(--blue-text); font-size: 1.8rem; margin-top: 5px;">{{ $student->year_level }}</h2>
    </div>

    <div style="background: {{ $student->isRegular() ? 'var(--reg-bg)' : 'var(--irreg-bg)' }}; padding: 1.5rem; border-radius: 20px; border: 1px solid {{ $student->isRegular() ? 'var(--reg-border)' : 'var(--irreg-border)' }};">
        <span style="color: {{ $student->isRegular() ? 'var(--reg-text)' : 'var(--irreg-text)' }}; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Status</span>
        <h2 style="color: {{ $student->isRegular() ? 'var(--reg-text)' : 'var(--irreg-text)' }}; font-size: 1.8rem; margin-top: 5px;">{{ $student->isRegular() ? 'Regular' : 'Irregular' }}</h2>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; color: var(--text-main);">Student Information</h3>
        <div style="display:flex; flex-direction:column; gap: 1rem;">
            <div style="display:flex; justify-content:space-between; border-bottom: 1px solid var(--border-light); padding-bottom: 0.5rem;">
                <span style="color: var(--text-muted);">Email</span>
                <span style="font-weight:600; color: var(--text-main);">{{ $student->email }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom: 1px solid var(--border-light); padding-bottom: 0.5rem;">
                <span style="color: var(--text-muted);">School</span>
                <span style="font-weight:600; color: var(--text-main);">{{ $student->school->name ?? 'N/A' }}</span>
            </div>
            <div style="background: var(--sky-bg); color: var(--sky-text); padding: 0.8rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600;">
                {{ $classificationInfo['message'] }}
            </div>
        </div>
    </div>

    <div class="card" style="display:flex; flex-direction:column; justify-content:center;">
        <h3 style="margin-bottom: 1rem; color: var(--text-main);">Payment Status</h3>
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

<div class="card" style="margin-top: 1.5rem;">
    <h3 style="margin-bottom: 1.5rem; color: var(--text-main);">Enrollment Status</h3>
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
            <a href="#" style="background: var(--reg-text); color: white; padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: background 0.3s;" class="btn">Start Enrollment</a>
        </div>
    @endif
</div>
@endsection