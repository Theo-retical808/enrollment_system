@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div style="margin-bottom: 2rem;">
    <p style="color: #64748b; font-weight: 500;">Welcome back, {{ $student->full_name }}</p>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: #fff4e5; padding: 1.5rem; border-radius: 20px; border: 1px solid #ffe8cc;">
        <span style="color: #c2780e; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Student ID</span>
        <h2 style="color: #c2780e; font-size: 1.8rem; margin-top: 5px;">{{ $student->student_id }}</h2>
    </div>
    
    <div style="background: #f3f0ff; padding: 1.5rem; border-radius: 20px; border: 1px solid #ebe5ff;">
        <span style="color: #6b21a8; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Year Level</span>
        <h2 style="color: #6b21a8; font-size: 1.8rem; margin-top: 5px;">{{ $student->year_level }}</h2>
    </div>

    <div style="background: {{ $student->isRegular() ? '#eff6ff' : '#fff7ed' }}; padding: 1.5rem; border-radius: 20px; border: 1px solid {{ $student->isRegular() ? '#bfdbfe' : '#ffedd5' }};">
        <span style="color: {{ $student->isRegular() ? '#1d4ed8' : '#9a3412' }}; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">Status</span>
        <h2 style="color: {{ $student->isRegular() ? '#1d4ed8' : '#9a3412' }}; font-size: 1.8rem; margin-top: 5px;">{{ $student->isRegular() ? 'Regular' : 'Irregular' }}</h2>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
    <div class="card">
        <h3 style="margin-bottom: 1.5rem;">Student Information</h3>
        <div style="display:flex; flex-direction:column; gap: 1rem;">
            <div style="display:flex; justify-content:space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                <span style="color:#64748b;">Email</span>
                <span style="font-weight:600;">{{ $student->email }}</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">
                <span style="color:#64748b;">School</span>
                <span style="font-weight:600;">{{ $student->school->name ?? 'N/A' }}</span>
            </div>
            <div style="background: #eff6ff; color: #1d4ed8; padding: 0.8rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600;">
                {{ $classificationInfo['message'] }}
            </div>
        </div>
    </div>

    <div class="card" style="display:flex; flex-direction:column; justify-content:center;">
        <h3 style="margin-bottom: 1rem;">Payment Status</h3>
        @if($paymentStatus['status'] === 'paid')
            <div style="background:#eff6ff; border-radius:12px; padding: 1.2rem; border-left: 4px solid #2563eb;">
                <p style="color:#1d4ed8; font-weight:700;">✓ Payment Completed</p>
                <p style="font-size:0.9rem; color:#1d4ed8;">Amount: ₱{{ number_format($paymentStatus['amount_paid'], 2) }}</p>
            </div>
        @else
            <div style="background:#fef2f2; border-radius:12px; padding: 1.2rem; border-left: 4px solid #ef4444; margin-bottom:1rem;">
                <p style="color:#991b1b; font-weight:700;">Payment Required</p>
                <p style="font-size:0.85rem;">{{ $paymentStatus['message'] }}</p>
            </div>
            <a href="{{ route('student.payment.required') }}" style="background:#0f172a; color:white; text-align:center; border-radius: 12px; padding: 0.8rem; text-decoration: none; font-weight: 700; display: block;" class="btn">Pay Now</a>
        @endif
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <h3 style="margin-bottom: 1.5rem;">Enrollment Status</h3>
    @if($currentEnrollment)
        <div style="background: #f8fafc; border-radius: 12px; padding: 1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                <div>
                    <span style="font-weight:700; color: #1e293b;">{{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}</span>
                    <p style="font-size: 0.85rem; color: #64748b;">Total Units: {{ $currentEnrollment->total_units }}</p>
                </div>
                <span style="padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; background: #e2e8f0; color: #475569;">
                    {{ $currentEnrollment->status }}
                </span>
            </div>
            
            @if($currentEnrollment->courses->count() > 0)
                <table style="width:100%; text-align:left; border-collapse:collapse;">
                    <tr style="color:#64748b; font-size:0.8rem; text-transform:uppercase;">
                        <th style="padding-bottom:10px; border-bottom: 1px solid #e2e8f0;">Course</th>
                        <th style="padding-bottom:10px; border-bottom: 1px solid #e2e8f0;">Schedule</th>
                    </tr>
                    @foreach($currentEnrollment->courses as $course)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding: 12px 0;">
                            <span style="font-weight:700; color:#2563eb;">{{ $course->course_code }}</span><br>
                            <span style="font-size:0.85rem; color:#64748b;">{{ $course->title }}</span>
                        </td>
                        <td style="font-size:0.85rem; padding: 12px 0;">
                            <span style="font-weight: 600; color: #1e293b;">{{ $course->pivot->schedule_day ?? 'TBA' }}</span><br>
                            <span style="color:#64748b;">{{ $course->pivot->room ?? 'No Room' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            @endif
        </div>
    @else
        <div style="text-align:center; padding: 2.5rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
            <p style="color:#64748b; margin-bottom:1.5rem; font-weight: 500;">No Current Enrollment Found</p>
            <a href="#" style="background:#2563eb; color:white; padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: background 0.3s;" class="btn">Start Enrollment</a>
        </div>
    @endif
</div>
@endsection