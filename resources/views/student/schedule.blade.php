@extends('layouts.student')

@section('title', 'My Schedule')

@section('content')
<div style="max-width: 1200px;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">
                @if($currentEnrollment->status === 'approved')
                    My Approved Schedule
                @elseif($currentEnrollment->status === 'submitted')
                    Schedule Under Review
                @else
                    Current Schedule (Draft)
                @endif
            </h1>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">View and manage your enrolled courses.</p>
        </div>
        
        @if($currentEnrollment && $currentEnrollment->status === 'approved')
            <div style="display: flex; gap: 0.75rem;" class="no-print">
                <button onclick="window.print()" class="btn" style="background: white; border: 1px solid #e2e8f0; color: #475569;">
                    🖨️ Print
                </button>
                <a href="{{ route('student.schedule.export.pdf') }}" target="_blank" class="btn" style="background: #2563eb; color: white;">
                    📄 Export PDF
                </a>
            </div>
        @endif
    </div>

    @if($currentEnrollment)
        <div style="margin-bottom: 2rem; padding: 1.5rem; border-radius: 12px; background: {{ $currentEnrollment->status === 'approved' ? '#f0fdf4' : ($currentEnrollment->status === 'submitted' ? '#fffbeb' : ($currentEnrollment->status === 'rejected' ? '#fef2f2' : '#f8fafc')) }}; border: 1px solid {{ $currentEnrollment->status === 'approved' ? '#dcfce7' : ($currentEnrollment->status === 'submitted' ? '#fef3c7' : ($currentEnrollment->status === 'rejected' ? '#fee2e2' : '#e2e8f0')) }};">
            
            @if($currentEnrollment->status === 'approved')
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: #dcfce7; color: #166534; padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p style="color: #166534; margin: 0; font-weight: 700; font-size: 1.05rem;">Enrollment Approved</p>
                        <p style="color: #15803d; margin: 0.25rem 0 0 0; font-size: 0.9rem;">This is your official schedule for {{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}.</p>
                        
                        @if($currentEnrollment->review_comments)
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #bbf7d0;">
                                <p style="color: #166534; margin: 0; font-size: 0.9rem;">
                                    <strong>Professor's Comments:</strong> {{ $currentEnrollment->review_comments }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($currentEnrollment->status === 'rejected')
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: #fee2e2; color: #991b1b; padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p style="color: #991b1b; margin: 0; font-weight: 700; font-size: 1.05rem;">Schedule Rejected</p>
                        <p style="color: #b91c1c; margin: 0.25rem 0 0 0; font-size: 0.9rem;">Please review the comments below and resubmit.</p>
                        
                        @if($currentEnrollment->review_comments)
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #fecaca;">
                                <p style="color: #991b1b; margin: 0; font-size: 0.9rem;">
                                    <strong>Professor's Comments:</strong> {{ $currentEnrollment->review_comments }}
                                </p>
                            </div>
                        @endif
                        
                        <div style="margin-top: 1rem;">
                            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: #ef4444; color: white;">
                                Revise and Resubmit
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($currentEnrollment->status === 'submitted')
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: #fef3c7; color: #b45309; padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p style="color: #b45309; margin: 0; font-weight: 700; font-size: 1.05rem;">Under Review</p>
                        <p style="color: #d97706; margin: 0.25rem 0 0 0; font-size: 0.9rem;">Your schedule has been submitted and is currently under review by your professor.</p>
                    </div>
                </div>

            @else
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: #e2e8f0; color: #475569; padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                    <div>
                        <p style="color: #334155; margin: 0; font-weight: 700; font-size: 1.05rem;">Draft Schedule</p>
                        <p style="color: #475569; margin: 0.25rem 0 0 0; font-size: 0.9rem;">Continue your enrollment to submit this schedule for approval.</p>
                        <div style="margin-top: 1rem;">
                            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: #2563eb; color: white;">
                                Continue Enrollment
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card" style="padding: 1.5rem; border: none; background: #eff6ff;">
                <h3 style="color: #1e3a8a; margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Semester</h3>
                <p style="font-size: 1.4rem; font-weight: 800; color: #2563eb; margin: 0;">{{ $currentEnrollment->semester }}</p>
                <p style="font-size: 0.85rem; color: #3b82f6; margin: 0; font-weight: 500;">{{ $currentEnrollment->academic_year }}</p>
            </div>
            
            <div class="card" style="padding: 1.5rem; border: none; background: #f8fafc;">
                <h3 style="color: #475569; margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Total Courses</h3>
                <p style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $currentEnrollment->courses->count() }}</p>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0; font-weight: 500;">Classes Enrolled</p>
            </div>
            
            <div class="card" style="padding: 1.5rem; border: none; background: #f8fafc;">
                <h3 style="color: #475569; margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Total Units</h3>
                <p style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $currentEnrollment->courses->sum('units') }}</p>
                <p style="font-size: 0.85rem; color: #64748b; margin: 0; font-weight: 500;">Out of 21 Max</p>
            </div>
        </div>

        @if($currentEnrollment->courses->count() > 0)
            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
                <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: white;">
                    <h3 style="color: #0f172a; margin: 0; font-weight: 700;">Course List</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <tr>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Course Code</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Course Title</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: center;">Units</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Schedule</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Room & Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentEnrollment->courses as $course)
                            <tr style="border-bottom: 1px solid #f1f5f9; background: white;">
                                <td style="padding: 1.2rem 1.5rem; font-weight: 700; color: #2563eb;">
                                    {{ $course->course_code }}
                                </td>
                                <td style="padding: 1.2rem 1.5rem;">
                                    <p style="font-weight: 600; color: #0f172a; margin: 0;">{{ $course->title }}</p>
                                    @if($course->description)
                                        <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0;">
                                            {{ Str::limit($course->description, 60) }}
                                        </p>
                                    @endif
                                </td>
                                <td style="padding: 1.2rem 1.5rem; text-align: center; font-weight: 700; color: #475569;">
                                    {{ $course->units }}
                                </td>
                                <td style="padding: 1.2rem 1.5rem;">
                                    @if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time)
                                        <p style="font-weight: 600; color: #0f172a; margin: 0;">{{ $course->pivot->schedule_day }}</p>
                                        <p style="font-size: 0.85rem; color: #64748b; margin: 0;">
                                            {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                            {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                        </p>
                                    @else
                                        <span style="color: #94a3b8; font-weight: 500; font-size: 0.9rem;">TBA</span>
                                    @endif
                                </td>
                                <td style="padding: 1.2rem 1.5rem;">
                                    <p style="font-weight: 600; color: #0f172a; margin: 0;">Room: {{ $course->pivot->room ?? 'TBA' }}</p>
                                    <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Prof: {{ $course->pivot->instructor ?? 'TBA' }}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
                <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: white;">
                    <h3 style="color: #0f172a; margin: 0; font-weight: 700;">Weekly Overview</h3>
                </div>
                <div style="padding: 1.5rem; background: #f8fafc;">
                    @php
                        $weeklySchedule = [];
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        
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
                        
                        foreach($weeklySchedule as $day => $courses) {
                            usort($weeklySchedule[$day], function($a, $b) {
                                return strtotime($a['start_time']) - strtotime($b['start_time']);
                            });
                        }
                    @endphp
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        @foreach($days as $day)
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.2rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <h4 style="color: #0f172a; margin-bottom: 1rem; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.5rem;">{{ $day }}</h4>
                                
                                @if(isset($weeklySchedule[$day]) && count($weeklySchedule[$day]) > 0)
                                    @foreach($weeklySchedule[$day] as $schedule)
                                        <div style="background: #eff6ff; padding: 0.8rem; margin-bottom: 0.75rem; border-radius: 8px; border-left: 4px solid #2563eb;">
                                            <p style="font-weight: 700; color: #1d4ed8; margin: 0; font-size: 0.85rem;">
                                                {{ $schedule['course']->course_code }}
                                            </p>
                                            <p style="font-size: 0.75rem; color: #3b82f6; font-weight: 600; margin: 0.25rem 0;">
                                                {{ date('g:i A', strtotime($schedule['start_time'])) }} - {{ date('g:i A', strtotime($schedule['end_time'])) }}
                                            </p>
                                            @if($schedule['room'])
                                                <p style="font-size: 0.75rem; color: #64748b; margin: 0; font-weight: 500;">
                                                    📍 {{ $schedule['room'] }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div style="padding: 1rem 0; text-align: center;">
                                        <p style="color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin: 0;">No classes</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @else
            <div class="card" style="text-align: center; padding: 4rem 2rem;">
                <svg style="width: 48px; height: 48px; color: #cbd5e1; margin: 0 auto 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <h3 style="color: #0f172a; font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">No Courses Enrolled</h3>
                <p style="color: #64748b; margin-bottom: 1.5rem;">You haven't selected any courses for the upcoming semester yet.</p>
                
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn" style="background: #2563eb; color: white;">Get Assigned Schedule</a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn" style="background: #2563eb; color: white;">Select Courses</a>
                @endif
            </div>
        @endif
        
    @else
        <div class="card" style="text-align: center; padding: 4rem 2rem;">
            <svg style="width: 48px; height: 48px; color: #cbd5e1; margin: 0 auto 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 style="color: #0f172a; font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">No Active Enrollment</h3>
            <p style="color: #64748b; margin-bottom: 1.5rem;">You do not have a current enrollment record. Start your enrollment process now.</p>
            
            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: #2563eb; color: white;">
                Start Enrollment
            </a>
        </div>
    @endif
</div>

<style>
@media print {
    .btn, nav, .sidebar, .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
        background: white;
    }

    .main-wrapper {
        margin-left: 0 !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }
}
</style>
@endsection