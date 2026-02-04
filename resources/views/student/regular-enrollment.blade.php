@extends('layouts.app')

@section('nav-links')
    <a href="{{ route('student.dashboard') }}" class="nav-link">Dashboard</a>
    <span style="color: #4f46e5;">{{ $student->full_name }}</span>
    <form method="POST" action="{{ route('student.logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Logout</button>
    </form>
@endsection

@section('content')
<div style="padding: 2rem 0;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #4f46e5; margin-bottom: 0.5rem;">Regular Student Enrollment</h1>
                <p style="color: #6b7280;">Your schedule has been automatically assigned based on your program curriculum.</p>
            </div>
            <div style="text-align: right;">
                <div style="background: #f0fdf4; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #bbf7d0; margin-bottom: 0.5rem;">
                    <p style="color: #16a34a; font-weight: 600; margin: 0;">Regular Student</p>
                </div>
                <p style="color: #6b7280; font-size: 0.875rem;">Status: {{ ucfirst($enrollment->status) }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Enrollment Summary -->
        <div class="grid grid-2" style="margin-bottom: 2rem;">
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Enrollment Information</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Student:</strong> {{ $student->full_name }}</p>
                    <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                    <p><strong>School:</strong> {{ $student->school->name }}</p>
                    <p><strong>Year Level:</strong> {{ $student->year_level }}</p>
                    <p><strong>Semester:</strong> {{ $enrollment->semester }}</p>
                    <p><strong>Academic Year:</strong> {{ $enrollment->academic_year }}</p>
                </div>
            </div>
            
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Schedule Summary</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Total Courses:</strong> {{ $enrollment->courses->count() }}</p>
                    <p><strong>Total Units:</strong> {{ $enrollment->total_units }}</p>
                    <p><strong>Status:</strong> 
                        <span style="color: {{ $enrollment->status === 'approved' ? '#16a34a' : ($enrollment->status === 'submitted' ? '#f59e0b' : '#6b7280') }};">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </p>
                    @if($enrollment->submitted_at)
                        <p><strong>Submitted:</strong> {{ $enrollment->submitted_at->format('M d, Y g:i A') }}</p>
                    @endif
                    @if($enrollment->professor)
                        <p><strong>Reviewer:</strong> {{ $enrollment->professor->full_name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assigned Schedule -->
        <div style="margin-bottom: 2rem;">
            <h3 style="color: #374151; margin-bottom: 1rem;">Your Assigned Schedule</h3>
            
            @if($enrollment->courses->count() > 0)
                <div style="overflow-x: auto; background: white; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #4f46e5; color: white;">
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Course Code</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Course Title</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Units</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Day</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Time</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Room</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollment->courses as $course)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem; font-weight: 600; color: #4f46e5;">{{ $course->course_code }}</td>
                                <td style="padding: 1rem;">{{ $course->title }}</td>
                                <td style="padding: 1rem; text-align: center;">{{ $course->units }}</td>
                                <td style="padding: 1rem;">{{ $course->pivot->schedule_day }}</td>
                                <td style="padding: 1rem;">
                                    {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                    {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                </td>
                                <td style="padding: 1rem;">{{ $course->pivot->room }}</td>
                                <td style="padding: 1rem;">{{ $course->pivot->instructor }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background: #f9fafb; font-weight: 600;">
                                <td colspan="2" style="padding: 1rem;">Total</td>
                                <td style="padding: 1rem; text-align: center; color: #4f46e5;">{{ $enrollment->total_units }} units</td>
                                <td colspan="4" style="padding: 1rem;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <p style="color: #6b7280; font-size: 1.125rem;">No courses assigned yet.</p>
                    <p style="color: #6b7280;">Please refresh the page or contact the registrar.</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            @if($enrollment->status === 'draft')
                <form method="POST" action="{{ route('student.enrollment.regular.submit') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        Submit for Approval
                    </button>
                </form>
                
                <form method="POST" action="{{ route('student.enrollment.regular.reset') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="padding: 0.75rem 2rem;" 
                            onclick="return confirm('Are you sure you want to reset your enrollment? This will generate a new schedule.')">
                        Reset Schedule
                    </button>
                </form>
            @elseif($enrollment->status === 'submitted')
                <div style="text-align: center; padding: 1rem; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 0.5rem;">
                    <p style="color: #d97706; font-weight: 600;">
                        ⏳ Your enrollment is pending approval from {{ $enrollment->professor->full_name ?? 'your assigned professor' }}.
                    </p>
                    <p style="color: #d97706; font-size: 0.875rem; margin-top: 0.5rem;">
                        You will be notified once your schedule has been reviewed.
                    </p>
                </div>
            @elseif($enrollment->status === 'approved')
                <div style="text-align: center; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem;">
                    <p style="color: #16a34a; font-weight: 600;">
                        ✅ Your enrollment has been approved!
                    </p>
                    <p style="color: #16a34a; font-size: 0.875rem; margin-top: 0.5rem;">
                        Your schedule is now finalized for this semester.
                    </p>
                </div>
            @elseif($enrollment->status === 'rejected')
                <div style="text-align: center; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <p style="color: #dc2626; font-weight: 600;">
                        ❌ Your enrollment was rejected.
                    </p>
                    @if($enrollment->review_comments)
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">
                            <strong>Comments:</strong> {{ $enrollment->review_comments }}
                        </p>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('student.enrollment.regular.reset') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        Create New Enrollment
                    </button>
                </form>
            @endif
            
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary" style="padding: 0.75rem 2rem;">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection