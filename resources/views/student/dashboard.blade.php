@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Student Dashboard</h1>
    <p class="page-subtitle">Welcome back, {{ $student->full_name }}</p>
</div>

<div class="grid grid-3">
    <div class="stat-card">
        <div class="stat-label">Student ID</div>
        <div class="stat-value">{{ $student->student_id }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
        <div class="stat-label">Year Level</div>
        <div class="stat-value">{{ $student->year_level }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, {{ $student->isRegular() ? '#10b981' : '#f59e0b' }} 0%, {{ $student->isRegular() ? '#059669' : '#d97706' }} 100%);">
        <div class="stat-label">Status</div>
        <div class="stat-value">{{ $student->isRegular() ? 'Regular' : 'Irregular' }}</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="grid grid-2">
    <div class="card">
        <h2 class="card-title">Student Information</h2>
        <table>
            <tbody>
                <tr>
                    <td style="font-weight: 500;">Email</td>
                    <td>{{ $student->email }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">School</td>
                    <td>{{ $student->school->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Classification</td>
                    <td>
                        <span class="badge {{ $student->isRegular() ? 'badge-success' : 'badge-warning' }}">
                            {{ $classificationInfo['message'] }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="card-title">Payment Status</h2>
        @if($paymentStatus['status'] === 'paid')
            <div class="alert alert-success">
                <strong>✓ Payment Completed</strong><br>
                Amount: ₱{{ number_format($paymentStatus['amount_paid'], 2) }}<br>
                Paid on: {{ $paymentStatus['paid_at']->format('M d, Y g:i A') }}
            </div>
        @else
            <div class="alert alert-error">
                <strong>Payment Required</strong><br>
                {{ $paymentStatus['message'] }}
                @if(isset($paymentStatus['amount_due']))
                    <br>Amount Due: ₱{{ number_format($paymentStatus['amount_due'], 2) }}
                @endif
            </div>
            <a href="{{ route('student.payment.required') }}" class="btn btn-primary">Pay Now</a>
        @endif
    </div>
</div>

<div class="card">
    <h2 class="card-title">Enrollment Status</h2>
    
    @if($currentEnrollment)
        <div class="alert alert-{{ $currentEnrollment->status === 'approved' ? 'success' : ($currentEnrollment->status === 'submitted' ? 'warning' : 'info') }}">
            <strong>
                @if($currentEnrollment->status === 'approved')
                    ✓ Enrollment Approved
                @elseif($currentEnrollment->status === 'submitted')
                    ⏳ Under Review
                @elseif($currentEnrollment->status === 'rejected')
                    ✗ Enrollment Rejected
                @else
                    📝 Draft
                @endif
            </strong><br>
            Semester: {{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}<br>
            Total Units: {{ $currentEnrollment->total_units }}
            
            @if($currentEnrollment->review_comments)
                <br><strong>Professor's Comments:</strong> {{ $currentEnrollment->review_comments }}
            @endif
        </div>

        @if($currentEnrollment->courses->count() > 0)
            <table style="margin-top: 1rem;">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Units</th>
                        <th>Schedule</th>
                        <th>Room</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentEnrollment->courses as $course)
                    <tr>
                        <td><strong style="color: #2563eb;">{{ $course->course_code }}</strong></td>
                        <td>{{ $course->title }}</td>
                        <td>{{ $course->units }}</td>
                        <td>
                            @if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time)
                                {{ $course->pivot->schedule_day }} 
                                {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                            @else
                                TBA
                            @endif
                        </td>
                        <td>{{ $course->pivot->room ?? 'TBA' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            @if($currentEnrollment->status === 'approved')
                <a href="{{ route('student.schedule') }}" class="btn btn-primary">View Full Schedule</a>
                <a href="{{ route('student.schedule.export.pdf') }}" class="btn btn-secondary" target="_blank">Export PDF</a>
                <a href="{{ route('student.schedule.export.csv') }}" class="btn btn-secondary">Export CSV</a>
            @elseif($currentEnrollment->status === 'rejected')
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary">Revise and Resubmit</a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary">Revise and Resubmit</a>
                @endif
            @elseif($currentEnrollment->status === 'submitted')
                <a href="{{ route('student.schedule') }}" class="btn btn-secondary">View Submitted Schedule</a>
            @elseif($currentEnrollment->status === 'draft')
                <a href="{{ route('student.schedule') }}" class="btn btn-secondary">View Draft Schedule</a>
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary">Continue Enrollment</a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary">Continue Enrollment</a>
                @endif
            @endif
        </div>
    @else
        <div class="alert alert-info">
            <strong>No Current Enrollment</strong><br>
            Start your enrollment process to register for courses.
        </div>
        
        <div style="margin-top: 1rem;">
            @if($canAccessEnrollment['can_access'])
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary">Get Assigned Schedule</a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary">Select Courses</a>
                @endif
            @else
                <div class="alert alert-error" style="margin-bottom: 1rem;">
                    <strong>Enrollment Locked:</strong> {{ $canAccessEnrollment['reason'] }}
                </div>
                <a href="{{ route('student.payment.required') }}" class="btn btn-primary">Complete Payment to Enroll</a>
            @endif
        </div>
    @endif
</div>
@endsection
