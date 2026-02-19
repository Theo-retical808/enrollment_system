@extends('layouts.app')

@section('nav-links')
    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
    <span style="color: #4f46e5;">{{ $student->full_name }}</span>
    <form method="POST" action="{{ route('student.logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Logout</button>
    </form>
@endsection

@section('content')
<div style="padding: 2rem 0;">
    <div class="card">
        <h1 style="color: #4f46e5; margin-bottom: 1rem;">
            @if($currentEnrollment->status === 'approved')
                My Approved Schedule
            @elseif($currentEnrollment->status === 'submitted')
                Schedule Under Review
            @else
                Current Schedule (Draft)
            @endif
        </h1>
        
        <!-- Enrollment Status Banner -->
        <div style="margin-bottom: 2rem; padding: 1rem; background: {{ $currentEnrollment->status === 'approved' ? '#f0fdf4' : ($currentEnrollment->status === 'submitted' ? '#fef3c7' : ($currentEnrollment->status === 'rejected' ? '#fef2f2' : '#f3f4f6')) }}; border: 1px solid {{ $currentEnrollment->status === 'approved' ? '#bbf7d0' : ($currentEnrollment->status === 'submitted' ? '#fcd34d' : ($currentEnrollment->status === 'rejected' ? '#fecaca' : '#d1d5db')) }}; border-radius: 0.5rem;">
            @if($currentEnrollment->status === 'approved')
                <p style="color: #16a34a; margin: 0; font-weight: 600;">
                    ✓ Your enrollment has been approved! This is your official schedule for {{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}.
                </p>
                @if($currentEnrollment->review_comments)
                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #bbf7d0;">
                        <p style="color: #16a34a; margin: 0; font-size: 0.875rem;">
                            <strong>Professor's Comments:</strong><br>
                            {{ $currentEnrollment->review_comments }}
                        </p>
                    </div>
                @endif
                @if($currentEnrollment->professor)
                    <p style="color: #16a34a; margin-top: 0.5rem; font-size: 0.875rem;">
                        <strong>Reviewed by:</strong> {{ $currentEnrollment->professor->full_name }}
                    </p>
                @endif
            @elseif($currentEnrollment->status === 'rejected')
                <p style="color: #dc2626; margin: 0; font-weight: 600;">
                    ❌ Your schedule has been rejected. Please review the comments below and resubmit.
                </p>
                @if($currentEnrollment->review_comments)
                    <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #fecaca;">
                        <p style="color: #dc2626; margin: 0; font-size: 0.875rem;">
                            <strong>Professor's Comments:</strong><br>
                            {{ $currentEnrollment->review_comments }}
                        </p>
                    </div>
                @endif
                @if($currentEnrollment->professor)
                    <p style="color: #dc2626; margin-top: 0.5rem; font-size: 0.875rem;">
                        <strong>Reviewed by:</strong> {{ $currentEnrollment->professor->full_name }}
                    </p>
                @endif
            @elseif($currentEnrollment->status === 'submitted')
                <p style="color: #d97706; margin: 0; font-weight: 600;">
                    ⏳ Your schedule has been submitted and is currently under review by your professor.
                </p>
                <p style="color: #d97706; margin-top: 0.5rem; font-size: 0.875rem;">
                    <strong>Submitted on:</strong> {{ $currentEnrollment->submitted_at->format('M d, Y g:i A') }}
                </p>
            @else
                <p style="color: #6b7280; margin: 0; font-weight: 600;">
                    📝 This is your draft schedule. Continue enrollment to submit for approval.
                </p>
            @endif
        </div>

        <!-- Schedule Summary -->
        <div class="grid grid-3" style="margin-bottom: 2rem;">
            <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                <h3 style="color: #374151; margin-bottom: 0.5rem;">Semester</h3>
                <p style="font-size: 1.125rem; font-weight: 600; color: #4f46e5; margin: 0;">
                    {{ $currentEnrollment->semester }}
                </p>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                    {{ $currentEnrollment->academic_year }}
                </p>
            </div>
            
            <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                <h3 style="color: #374151; margin-bottom: 0.5rem;">Total Courses</h3>
                <p style="font-size: 1.125rem; font-weight: 600; color: #4f46e5; margin: 0;">
                    {{ $currentEnrollment->courses->count() }}
                </p>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                    courses enrolled
                </p>
            </div>
            
            <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                <h3 style="color: #374151; margin-bottom: 0.5rem;">Total Units</h3>
                <p style="font-size: 1.125rem; font-weight: 600; color: #4f46e5; margin: 0;">
                    {{ $currentEnrollment->courses->sum('units') }}
                </p>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                    out of 21 max
                </p>
            </div>
        </div>

        @if($currentEnrollment->courses->count() > 0)
            <!-- Course Schedule Table -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #4f46e5; color: white;">
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Course Code</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Course Title</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 600;">Units</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Schedule</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Room</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600;">Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentEnrollment->courses as $course)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem; font-weight: 600; color: #4f46e5;">
                                    {{ $course->course_code }}
                                </td>
                                <td style="padding: 1rem;">
                                    <div>
                                        <p style="font-weight: 600; margin: 0;">{{ $course->title }}</p>
                                        @if($course->description)
                                            <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                                                {{ Str::limit($course->description, 100) }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: center; font-weight: 600;">
                                    {{ $course->units }}
                                </td>
                                <td style="padding: 1rem;">
                                    @if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time)
                                        <div>
                                            <p style="font-weight: 600; margin: 0;">{{ $course->pivot->schedule_day }}</p>
                                            <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">
                                                {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                                {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                            </p>
                                        </div>
                                    @else
                                        <span style="color: #9ca3af;">TBA</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <span style="font-weight: 500;">{{ $course->pivot->room ?? 'TBA' }}</span>
                                </td>
                                <td style="padding: 1rem;">
                                    <span style="font-weight: 500;">{{ $course->pivot->instructor ?? 'TBA' }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Weekly Schedule View -->
            <div style="margin-top: 2rem;">
                <h3 style="color: #374151; margin-bottom: 1rem;">Weekly Schedule</h3>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem;">
                    @php
                        $weeklySchedule = [];
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        
                        foreach($currentEnrollment->courses as $course) {
                            if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time) {
                                $weeklySchedule[$course->pivot->schedule_day][] = [
                                    'course' => $course,
                                    'start_time' => $course->pivot->start_time,
                                    'end_time' => $course->pivot->end_time,
                                    'room' => $course->pivot->room
                                ];
                            }
                        }
                        
                        // Sort each day's schedule by start time
                        foreach($weeklySchedule as $day => $courses) {
                            usort($weeklySchedule[$day], function($a, $b) {
                                return strtotime($a['start_time']) - strtotime($b['start_time']);
                            });
                        }
                    @endphp
                    
                    <div class="grid grid-2" style="gap: 1rem;">
                        @foreach($days as $day)
                            <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                                <h4 style="color: #374151; margin-bottom: 0.75rem; font-weight: 600;">{{ $day }}</h4>
                                @if(isset($weeklySchedule[$day]) && count($weeklySchedule[$day]) > 0)
                                    @foreach($weeklySchedule[$day] as $schedule)
                                        <div style="background: white; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 0.375rem; border-left: 4px solid #4f46e5;">
                                            <p style="font-weight: 600; color: #4f46e5; margin: 0; font-size: 0.875rem;">
                                                {{ $schedule['course']->course_code }}
                                            </p>
                                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0;">
                                                {{ date('g:i A', strtotime($schedule['start_time'])) }} - 
                                                {{ date('g:i A', strtotime($schedule['end_time'])) }}
                                            </p>
                                            @if($schedule['room'])
                                                <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">
                                                    📍 {{ $schedule['room'] }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">No classes</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 2rem; text-align: center;">
                @if($currentEnrollment->status === 'approved')
                    <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                        <button onclick="window.print()" class="btn btn-primary">
                            🖨️ Print Schedule
                        </button>
                        <a href="{{ route('student.schedule.export.pdf') }}" class="btn btn-primary" target="_blank">
                            📄 Export as PDF
                        </a>
                        <a href="{{ route('student.schedule.export.csv') }}" class="btn btn-primary">
                            📊 Export as CSV
                        </a>
                        <form method="POST" action="{{ route('student.schedule.email') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                📧 Email Schedule
                            </button>
                        </form>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                            Back to Dashboard
                        </a>
                    </div>
                @elseif($currentEnrollment->status === 'rejected')
                    @if($student->isRegular())
                        <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary" style="margin-right: 1rem;">
                            Revise and Resubmit
                        </a>
                    @else
                        <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary" style="margin-right: 1rem;">
                            Revise and Resubmit
                        </a>
                    @endif
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                @elseif($currentEnrollment->status === 'draft')
                    @if($student->isRegular())
                        <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary" style="margin-right: 1rem;">
                            Continue Enrollment
                        </a>
                    @else
                        <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary" style="margin-right: 1rem;">
                            Continue Enrollment
                        </a>
                    @endif
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                @endif
            </div>
        @else
            <!-- No Courses Enrolled -->
            <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem;">
                <p style="color: #6b7280; font-size: 1.125rem; margin-bottom: 1rem;">
                    No courses enrolled yet.
                </p>
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary">
                        Get Assigned Schedule
                    </a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary">
                        Select Courses
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    .btn, nav, .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .card {
        box-shadow: none;
        border: none;
    }
}
</style>
@endsection