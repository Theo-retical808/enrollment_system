@extends('layouts.student')

@section('title', 'My Schedule')

@section('content')

<style>
    :root {
        /* General Colors */
        --text-main: #0f172a;
        --text-muted: #64748b;
        --text-light: #475569;
        --bg-main: #ffffff;
        --border-light: #f1f5f9;
        
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --table-header-bg: #f8fafc;
        
        /* Gradient Colors */
        --grad-start: #1e3a8a;
        --grad-end: #2563eb;
        
        /* Blue Theme */
        --blue-bg: #dbeafe;
        --blue-text: #2563eb;
        
        /* Sky Theme */
        --sky-bg: #e0f2fe;
        --sky-border: #bae6fd;
        --sky-text: #0369a1;
        --sky-text-dark: #075985;
        --sky-text-muted: #0c4a6e;
        
        /* Success Theme (Approved) */
        --success-bg-light: #f0fdf4;
        --success-bg: #dcfce7;
        --success-border: #dcfce7;
        --success-text: #166534;
        --success-text-muted: #15803d;
        --success-grade: #059669;
        
        /* Danger Theme (Rejected) */
        --danger-bg-light: #fef2f2;
        --danger-bg: #fee2e2;
        --danger-border: #fee2e2;
        --danger-text: #991b1b;
        --danger-text-muted: #b91c1c;
        --danger-grade: #dc2626;
        --danger-icon: #ef4444;

        /* Warning Theme (Submitted) - Added for Schedule Status */
        --warning-bg-light: #fffbeb;
        --warning-bg: #fef3c7;
        --warning-border: #fef3c7;
        --warning-text: #b45309;
        --warning-text-muted: #d97706;

        /* Draft Theme - Added for Schedule Status */
        --draft-bg-light: #f8fafc;
        --draft-bg: #e2e8f0;
        --draft-text: #334155;
    }

    /* Dark Mode Colors */
    .dark, [data-theme="dark"], [data-bs-theme="dark"] {
        /* General Colors */
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --text-light: #cbd5e1;
        --bg-main: #0f172a;
        --border-light: #334155;
        
        --card-bg: #1e293b;
        --card-border: #334155;
        --table-header-bg: #0f172a;
        
        /* Gradient Colors */
        --grad-start: #1e40af;
        --grad-end: #3b82f6;
        
        /* Blue Theme */
        --blue-bg: #1e3a8a;
        --blue-text: #bfdbfe;
        
        /* Sky Theme */
        --sky-bg: #0c4a6e;
        --sky-border: #075985;
        --sky-text: #bae6fd;
        --sky-text-dark: #e0f2fe;
        --sky-text-muted: #7dd3fc;
        
        /* Success Theme (Approved) */
        --success-bg-light: #022c22;
        --success-bg: #064e3b;
        --success-border: #065f46;
        --success-text: #6ee7b7;
        --success-text-muted: #34d399;
        --success-grade: #10b981;
        
        /* Danger Theme (Rejected) */
        --danger-bg-light: #450a0a;
        --danger-bg: #7f1d1d;
        --danger-border: #991b1b;
        --danger-text: #fca5a5;
        --danger-text-muted: #f87171;
        --danger-grade: #ef4444;
        --danger-icon: #f87171;

        /* Warning Theme (Submitted) */
        --warning-bg-light: #451a03;
        --warning-bg: #78350f;
        --warning-border: #92400e;
        --warning-text: #fde68a;
        --warning-text-muted: #fcd34d;

        /* Draft Theme */
        --draft-bg-light: #0f172a;
        --draft-bg: #1e293b;
        --draft-text: #cbd5e1;
    }

    @media print {
        .btn, nav, .sidebar, .no-print { display: none !important; }
        body { font-size: 12px; background: white !important; color: black !important; }
        .main-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; background: white !important; }
        * { color: black !important; }
    }
</style>

<div style="max-width: 1200px;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="color: var(--text-main); font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem;">
                @if($currentEnrollment->status === 'approved')
                    My Approved Schedule
                @elseif($currentEnrollment->status === 'submitted')
                    Schedule Under Review
                @else
                    Current Schedule (Draft)
                @endif
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">View and manage your enrolled courses.</p>
        </div>
        
        @if($currentEnrollment && $currentEnrollment->status === 'approved')
            <div style="display: flex; gap: 0.75rem;" class="no-print">
                <button onclick="window.print()" class="btn" style="background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);">
                    🖨️ Print
                </button>
                <a href="{{ route('student.schedule.export.pdf') }}" target="_blank" class="btn" style="background: var(--grad-end); color: white; border: none;">
                    📄 Export PDF
                </a>
            </div>
        @endif
    </div>

    @if($currentEnrollment)
        <div style="margin-bottom: 2rem; padding: 1.5rem; border-radius: 12px; background: {{ $currentEnrollment->status === 'approved' ? 'var(--success-bg-light)' : ($currentEnrollment->status === 'submitted' ? 'var(--warning-bg-light)' : ($currentEnrollment->status === 'rejected' ? 'var(--danger-bg-light)' : 'var(--draft-bg-light)')) }}; border: 1px solid {{ $currentEnrollment->status === 'approved' ? 'var(--success-border)' : ($currentEnrollment->status === 'submitted' ? 'var(--warning-border)' : ($currentEnrollment->status === 'rejected' ? 'var(--danger-border)' : 'var(--card-border)')) }};">
            
            @if($currentEnrollment->status === 'approved')
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: var(--success-bg); color: var(--success-text); padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p style="color: var(--success-text); margin: 0; font-weight: 700; font-size: 1.05rem;">Enrollment Approved</p>
                        <p style="color: var(--success-text-muted); margin: 0.25rem 0 0 0; font-size: 0.9rem;">This is your official schedule for {{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}.</p>
                    </div>
                </div>

            @elseif($currentEnrollment->status === 'rejected')
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: var(--danger-bg); color: var(--danger-text); padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p style="color: var(--danger-text); margin: 0; font-weight: 700; font-size: 1.05rem;">Schedule Rejected</p>
                        <p style="color: var(--danger-text-muted); margin: 0.25rem 0 0 0; font-size: 0.9rem;">Please review the comments below and resubmit.</p>
                        <div style="margin-top: 1rem;">
                            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: var(--danger-icon); color: white; border: none;">
                                Revise and Resubmit
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($currentEnrollment->status === 'submitted')
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: var(--warning-bg); color: var(--warning-text); padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p style="color: var(--warning-text); margin: 0; font-weight: 700; font-size: 1.05rem;">Under Review</p>
                        <p style="color: var(--warning-text-muted); margin: 0.25rem 0 0 0; font-size: 0.9rem;">Your schedule has been submitted and is currently under review by your professor.</p>
                    </div>
                </div>

            @else
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="background: var(--draft-bg); color: var(--draft-text); padding: 8px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                    <div>
                        <p style="color: var(--draft-text); margin: 0; font-weight: 700; font-size: 1.05rem;">Draft Schedule</p>
                        <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.9rem;">Continue your enrollment to submit this schedule for approval.</p>
                        <div style="margin-top: 1rem;">
                            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: var(--grad-end); color: white; border: none;">
                                Continue Enrollment
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--card-border); background: var(--card-bg);">
                <h3 style="color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Semester</h3>
                <p style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">{{ $currentEnrollment->semester }}</p>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; font-weight: 500;">{{ $currentEnrollment->academic_year }}</p>
            </div>
            
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--card-border); background: var(--card-bg);">
                <h3 style="color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Total Courses</h3>
                <p style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">{{ $currentEnrollment->courses->count() }}</p>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; font-weight: 500;">Classes Enrolled</p>
            </div>
            
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--card-border); background: var(--card-bg);">
                <h3 style="color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.85rem; text-transform: uppercase; font-weight: 700;">Total Units</h3>
                <p style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">{{ $currentEnrollment->courses->sum('units') }}</p>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; font-weight: 500;">Out of 21 Max</p>
            </div>
        </div>

        @if($currentEnrollment->courses->count() > 0)
            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem; border: 1px solid var(--card-border); background: var(--card-bg);">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light); background: var(--card-bg);">
                    <h3 style="color: var(--text-main); margin: 0; font-weight: 700;">Course List</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead style="background: var(--table-header-bg); border-bottom: 1px solid var(--card-border);">
                            <tr>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Course Code</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Course Title</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: center;">Units</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Schedule</th>
                                <th style="padding: 1rem 1.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Room & Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentEnrollment->courses as $course)
                            <tr style="border-bottom: 1px solid var(--border-light); background: var(--card-bg);">
                                <td style="padding: 1.2rem 1.5rem; font-weight: 700; color: var(--text-main);">
                                    {{ $course->course_code }}
                                </td>
                                <td style="padding: 1.2rem 1.5rem;">
                                    <p style="font-weight: 600; color: var(--text-main); margin: 0;">{{ $course->title }}</p>
                                    @if($course->description)
                                        <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0.25rem 0 0 0;">
                                            {{ Str::limit($course->description, 60) }}
                                        </p>
                                    @endif
                                </td>
                                <td style="padding: 1.2rem 1.5rem; text-align: center; font-weight: 700; color: var(--text-muted);">
                                    {{ $course->units }}
                                </td>
                                <td style="padding: 1.2rem 1.5rem;">
                                    @if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time)
                                        <p style="font-weight: 600; color: var(--text-main); margin: 0;">{{ $course->pivot->schedule_day }}</p>
                                        <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">
                                            {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                            {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                        </p>
                                    @else
                                        <span style="color: var(--text-light); font-weight: 500; font-size: 0.9rem;">TBA</span>
                                    @endif
                                </td>
                                <td style="padding: 1.2rem 1.5rem;">
                                    <p style="font-weight: 600; color: var(--text-main); margin: 0;">Room: {{ $course->pivot->room ?? 'TBA' }}</p>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Prof: {{ $course->pivot->instructor ?? 'TBA' }}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 2rem; border: 1px solid var(--card-border); background: var(--card-bg);">
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light); background: var(--card-bg);">
                    <h3 style="color: var(--text-main); margin: 0; font-weight: 700;">Weekly Overview</h3>
                </div>
                <div style="padding: 1.5rem; background: var(--table-header-bg);">
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
                            <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.2rem; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <h4 style="color: var(--text-main); margin-bottom: 1rem; font-weight: 700; border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem;">{{ $day }}</h4>
                                
                                @if(isset($weeklySchedule[$day]) && count($weeklySchedule[$day]) > 0)
                                    @foreach($weeklySchedule[$day] as $schedule)
                                        <div style="background: var(--table-header-bg); padding: 0.8rem; margin-bottom: 0.75rem; border-radius: 8px; border-left: 4px solid var(--grad-end); border-top: 1px solid var(--card-border); border-right: 1px solid var(--card-border); border-bottom: 1px solid var(--card-border);">
                                            <p style="font-weight: 700; color: var(--text-main); margin: 0; font-size: 0.85rem;">
                                                {{ $schedule['course']->course_code }}
                                            </p>
                                            <p style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin: 0.25rem 0;">
                                                {{ date('g:i A', strtotime($schedule['start_time'])) }} - {{ date('g:i A', strtotime($schedule['end_time'])) }}
                                            </p>
                                            @if($schedule['room'])
                                                <p style="font-size: 0.75rem; color: var(--text-light); margin: 0; font-weight: 500;">
                                                    📍 {{ $schedule['room'] }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div style="padding: 1rem 0; text-align: center;">
                                        <p style="color: var(--text-light); font-size: 0.85rem; font-weight: 500; margin: 0;">No classes</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @else
            <div class="card" style="text-align: center; padding: 4rem 2rem; background: var(--card-bg); border: 1px solid var(--card-border);">
                <svg style="width: 48px; height: 48px; color: var(--text-light); margin: 0 auto 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <h3 style="color: var(--text-main); font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">No Courses Enrolled</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't selected any courses for the upcoming semester yet.</p>
                
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn" style="background: var(--grad-end); color: white; border: none;">Get Assigned Schedule</a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn" style="background: var(--grad-end); color: white; border: none;">Select Courses</a>
                @endif
            </div>
        @endif
        
    @else
        <div class="card" style="text-align: center; padding: 4rem 2rem; background: var(--card-bg); border: 1px solid var(--card-border);">
            <svg style="width: 48px; height: 48px; color: var(--text-light); margin: 0 auto 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 style="color: var(--text-main); font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">No Active Enrollment</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You do not have a current enrollment record. Start your enrollment process now.</p>
            
            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: var(--grad-end); color: white; border: none;">
                Start Enrollment
            </a>
        </div>
    @endif
</div>
@endsection