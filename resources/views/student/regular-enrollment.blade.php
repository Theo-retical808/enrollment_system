@extends('layouts.student')

@section('title', 'Regular Enrollment')

@section('content')
<div class="page-header">
    <h1 class="page-title">Regular Student Enrollment</h1>
    <p class="page-subtitle">Your schedule has been automatically assigned based on your program curriculum</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="grid grid-2">
    <div class="card">
        <h2 class="card-title">Enrollment Information</h2>
        <table>
            <tbody>
                <tr>
                    <td style="font-weight: 500;">Student</td>
                    <td>{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Student ID</td>
                    <td>{{ $student->student_id }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">School</td>
                    <td>{{ $student->school->name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Year Level</td>
                    <td>{{ $student->year_level }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Semester</td>
                    <td>{{ $enrollment->semester }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Academic Year</td>
                    <td>{{ $enrollment->academic_year }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="card-title">Schedule Summary</h2>
        <table>
            <tbody>
                <tr>
                    <td style="font-weight: 500;">Total Courses</td>
                    <td>{{ $enrollment->courses->count() }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Total Units</td>
                    <td>{{ $enrollment->total_units }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Status</td>
                    <td>
                        <span class="badge badge-{{ $enrollment->status === 'approved' ? 'success' : ($enrollment->status === 'submitted' ? 'warning' : 'info') }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </td>
                </tr>
                @if($enrollment->submitted_at)
                <tr>
                    <td style="font-weight: 500;">Submitted</td>
                    <td>{{ $enrollment->submitted_at->format('M d, Y g:i A') }}</td>
                </tr>
                @endif
                @if($enrollment->professor)
                <tr>
                    <td style="font-weight: 500;">Reviewer</td>
                    <td>{{ $enrollment->professor->full_name }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2 class="card-title">Your Assigned Schedule</h2>
    
    @if($enrollment->courses->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Units</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Room</th>
                    <th>Instructor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollment->courses as $course)
                <tr>
                    <td><strong style="color: #2563eb;">{{ $course->course_code }}</strong></td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->units }}</td>
                    <td>{{ $course->pivot->schedule_day }}</td>
                    <td>
                        {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                        {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                    </td>
                    <td>{{ $course->pivot->room }}</td>
                    <td>{{ $course->pivot->instructor }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 600;">
                    <td colspan="2">Total</td>
                    <td style="color: #2563eb;">{{ $enrollment->total_units }} units</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="alert alert-info">
            <strong>No courses assigned yet.</strong><br>
            Please refresh the page or contact the registrar.
        </div>
    @endif
</div>

<div class="card">
    @if($enrollment->status === 'draft')
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <form method="POST" action="{{ route('student.enrollment.regular.submit') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Submit for Approval</button>
            </form>
            
            <form method="POST" action="{{ route('student.enrollment.regular.reset') }}">
                @csrf
                <button type="submit" class="btn btn-secondary" 
                        onclick="return confirm('Are you sure you want to reset your enrollment? This will generate a new schedule.')">
                    Reset Schedule
                </button>
            </form>
        </div>
    @elseif($enrollment->status === 'submitted')
        <div class="alert alert-warning">
            <strong>⏳ Pending Approval</strong><br>
            Your enrollment is pending approval from {{ $enrollment->professor->full_name ?? 'your assigned professor' }}.<br>
            You will be notified once your schedule has been reviewed.
        </div>
    @elseif($enrollment->status === 'approved')
        <div class="alert alert-success">
            <strong>✓ Enrollment Approved</strong><br>
            Your schedule is now finalized for this semester.
        </div>
    @elseif($enrollment->status === 'rejected')
        <div class="alert alert-error">
            <strong>✗ Enrollment Rejected</strong><br>
            @if($enrollment->review_comments)
                <strong>Comments:</strong> {{ $enrollment->review_comments }}
            @endif
        </div>
        
        <form method="POST" action="{{ route('student.enrollment.regular.reset') }}" style="margin-top: 1rem;">
            @csrf
            <button type="submit" class="btn btn-primary">Create New Enrollment</button>
        </form>
    @endif
    
    <div style="text-align: center; margin-top: 1rem;">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
@endsection
