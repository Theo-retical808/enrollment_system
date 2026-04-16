@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')

<div class="page-header mb-8">
    <p class="text-muted font-bold" style="letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.8rem;">Welcome back</p>
    <h1 class="text-main font-extrabold" style="font-size: 2.2rem;">{{ $student->full_name }}</h1>
</div>

<!-- Stats Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Student ID Card -->
    <div class="card" style="background: var(--status-info-bg);">
        <div class="flex items-center gap-4 mb-4">
            <div style="background: white; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--udd-blue); shadow: var(--shadow-sm);">
                <i data-lucide="id-card" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Student ID</span>
                <h2 class="font-extrabold" style="color: var(--status-info-text); font-size: 1.75rem;">{{ $student->student_id }}</h2>
            </div>
        </div>
    </div>
    
    <!-- Year Level Card -->
    <div class="card" style="background: var(--udd-blue-light);">
        <div class="flex items-center gap-4 mb-4">
            <div style="background: white; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--udd-blue); shadow: var(--shadow-sm);">
                <i data-lucide="graduation-cap" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Year Level</span>
                <h2 class="font-extrabold" style="color: var(--udd-blue); font-size: 1.75rem;">Level {{ $student->year_level }}</h2>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    @php
        $statusTheme = $student->isRegular() ? 'success' : 'warning';
    @endphp
    <div class="card" style="background: var(--status-{{ $statusTheme }}-bg);">
        <div class="flex items-center gap-4 mb-4">
            <div style="background: white; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--status-{{ $statusTheme }}-text); shadow: var(--shadow-sm);">
                <i data-lucide="{{ $student->isRegular() ? 'user-check' : 'user-cog' }}" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Classification</span>
                <h2 class="font-extrabold" style="color: var(--status-{{ $statusTheme }}-text); font-size: 1.75rem;">{{ $student->isRegular() ? 'Regular' : 'Irregular' }}</h2>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Profile Info -->
    <div class="card">
        <div class="flex items-center gap-2 mb-6">
            <i data-lucide="info" class="text-muted" style="width: 20px;"></i>
            <h3 class="font-extrabold text-main" style="font-size: 1.25rem;">Academic Profile</h3>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div class="flex justify-between items-center" style="padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-light);">
                <span class="text-muted">Registered Email</span>
                <span class="text-main font-bold">{{ $student->email }}</span>
            </div>
            <div class="flex justify-between items-center" style="padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-light);">
                <span class="text-muted">School Affiliation</span>
                <span class="text-main font-bold">{{ $student->school->name ?? 'N/A' }}</span>
            </div>
            <div style="background: var(--status-info-bg); color: var(--status-info-text); padding: 1rem; border-radius: 12px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: flex-start; gap: 10px;">
                <i data-lucide="bell" style="width: 18px; margin-top: 2px;"></i>
                {{ $classificationInfo['message'] }}
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: center; min-height: 200px;">
        <div class="flex items-center gap-2 mb-6">
            <i data-lucide="wallet" class="text-muted" style="width: 20px;"></i>
            <h3 class="font-extrabold text-main" style="font-size: 1.25rem;">Financial Status</h3>
        </div>
        
        @if($paymentStatus['status'] === 'paid')
            <div style="background: var(--status-success-bg); border-radius: 16px; padding: 1.5rem; position: relative; overflow: hidden;">
                <div style="position: absolute; right: -10px; bottom: -10px; opacity: 0.1; color: var(--status-success-text);">
                    <i data-lucide="check-circle" style="width: 100px; height: 100px;"></i>
                </div>
                <div class="flex items-center gap-3 mb-2">
                    <i data-lucide="check-circle-2" style="color: var(--status-success-text);"></i>
                    <p style="color: var(--status-success-text); font-weight: 800; font-size: 1.1rem;">Payment Completed</p>
                </div>
                <p style="font-size: 1rem; color: var(--status-success-text); font-weight: 600;">Amount Settled: ₱{{ number_format((float)$paymentStatus['amount_paid'], 2) }}</p>
            </div>
        @else
            <div style="background: var(--status-danger-bg); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div class="flex items-center gap-3 mb-2">
                    <i data-lucide="alert-circle" style="color: var(--status-danger-text);"></i>
                    <p style="color: var(--status-danger-text); font-weight: 800; font-size: 1.1rem;">Payment Required</p>
                </div>
                <p style="font-size: 0.9rem; color: var(--status-danger-text); font-weight: 600;">{{ $paymentStatus['message'] }}</p>
            </div>
            <a href="{{ route('student.payment.required') }}" class="btn btn-primary btn-block">
                <i data-lucide="credit-card" style="width: 18px;"></i>
                Pay Now
            </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-2">
            <i data-lucide="clipboard-list" class="text-muted" style="width: 20px;"></i>
            <h3 class="font-extrabold text-main" style="font-size: 1.25rem;">Enrollment Overview</h3>
        </div>
        @if($currentEnrollment)
            <span class="badge badge-info">
                <i data-lucide="clock" style="width: 12px;"></i>
                {{ $currentEnrollment->status }}
            </span>
        @endif
    </div>

    @if($currentEnrollment)
        <div style="background: var(--bg-primary); border-radius: 12px; padding: 1.5rem;">
            <div class="flex justify-between items-center mb-6" style="border-bottom: 2px solid var(--border-light); padding-bottom: 1rem;">
                <div>
                    <h4 class="font-extrabold text-main" style="font-size: 1.1rem;">{{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}</h4>
                    <p class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Total Course Load: <span class="text-main">{{ $currentEnrollment->total_units }} Units</span></p>
                </div>
            </div>
            
            @if($currentEnrollment->courses->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width:100%; text-align:left; border-collapse:collapse;">
                        <thead>
                            <tr style="color: var(--text-muted); font-size:0.75rem; text-transform:uppercase; letter-spacing: 0.05em;">
                                <th style="padding: 1rem; border-bottom: 1px solid var(--border-main);">Course Details</th>
                                <th style="padding: 1rem; border-bottom: 1px solid var(--border-main);">Time & Location</th>
                                <th style="padding: 1rem; border-bottom: 1px solid var(--border-main); text-align: center;">Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentEnrollment->courses as $course)
                            <tr style="border-bottom:1px solid var(--border-light); transition: background 0.2s;" onmouseover="this.style.background='var(--border-light)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.25rem 1rem;">
                                    <span class="font-extrabold" style="color: var(--udd-blue); font-size: 1rem;">{{ $course->course_code }}</span><br>
                                    <span class="text-muted" style="font-size:0.85rem; font-weight: 600;">{{ $course->title }}</span>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i data-lucide="clock" style="width: 14px; color: var(--text-muted);"></i>
                                        <span class="text-main font-bold" style="font-size:0.9rem;">{{ $course->pivot->schedule_day ?? 'TBA' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="map-pin" style="width: 14px; color: var(--text-muted);"></i>
                                        <span class="text-muted" style="font-size:0.85rem; font-weight: 600;">{{ $course->pivot->room ?? 'No Room Assigned' }}</span>
                                    </div>
                                </td>
                                <td style="padding: 1.25rem 1rem; text-align: center;">
                                    <span class="badge badge-info" style="font-size: 0.9rem;">{{ $course->units }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div style="text-align:center; padding: 4rem 2.5rem; background: var(--bg-primary); border-radius: 16px; border: 2px dashed var(--border-main);">
            <div style="margin-bottom: 1.5rem; opacity: 0.5;">
                <i data-lucide="file-warning" style="width: 64px; height: 64px; color: var(--text-muted);"></i>
            </div>
            <h3 class="text-main font-extrabold" style="margin-bottom: 0.5rem;">No Current Enrollment</h3>
            <p class="text-muted" style="margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto; font-weight: 600;">You haven't submitted your enrollment for this semester yet.</p>
            <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary" style="padding: 1rem 3rem;">
                <i data-lucide="plus-circle" style="width: 20px;"></i>
                Start Enrollment Process
            </a>
        </div>
    @endif
</div>
@endsection