@extends('layouts.student')

@section('title', 'My Schedule')

@section('content')

<style>
    :root {
        /* Map page variables to global layout theme variables */
        --text-main: var(--text-primary);
        --text-muted: var(--text-secondary);
        --text-light: var(--text-secondary);
        --bg-main: var(--primary-bg);
        --border-light: var(--border-light);

        --card-bg: var(--bg-white);
        --card-border: var(--border-light);
        --table-header-bg: var(--bg-white);

        --grad-start: var(--accent-blue);
        --grad-end: var(--accent-blue-text);

        --blue-bg: var(--accent-blue);
        --blue-text: var(--accent-blue-text);

        --sky-bg: var(--accent-blue);
        --sky-border: var(--border-light);
        --sky-text: var(--accent-blue-text);
        --sky-text-dark: var(--accent-blue-text);
        --sky-text-muted: var(--text-secondary);

        --success-bg-light: var(--card-bg);
        --success-bg: var(--card-bg);
        --success-border: var(--card-border);
        --success-text: var(--text-primary);
        --success-text-muted: var(--text-secondary);
        --success-grade: var(--text-primary);

        --danger-bg-light: var(--card-bg);
        --danger-bg: var(--card-bg);
        --danger-border: var(--card-border);
        --danger-text: var(--text-primary);
        --danger-text-muted: var(--text-secondary);
        --danger-grade: var(--text-primary);
        --danger-icon: var(--accent-blue-text);

        --warning-bg-light: var(--card-bg);
        --warning-bg: var(--card-bg);
        --warning-border: var(--card-border);
        --warning-text: var(--text-primary);
        --warning-text-muted: var(--text-secondary);

        --draft-bg-light: var(--card-bg);
        --draft-bg: var(--card-bg);
        --draft-text: var(--text-primary);
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
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.1rem 1.25rem; border: none; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 0.85rem; border-radius: 12px;">
            <div style="background: #eff6ff; color: #3b82f6; padding: 0.7rem; border-radius: 10px;">
                <i data-lucide="graduation-cap" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h3 style="color: #64748b; margin: 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.025em;">Semester</h3>
                <p style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">{{ $currentEnrollment->semester }}</p>
            </div>
        </div>
        
        <div class="card" style="padding: 1.1rem 1.25rem; border: none; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 0.85rem; border-radius: 12px;">
            <div style="background: #fdf2f8; color: #ec4899; padding: 0.7rem; border-radius: 10px;">
                <i data-lucide="book-open" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h3 style="color: #64748b; margin: 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.025em;">Courses</h3>
                <p style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">{{ $currentEnrollment->courses->count() }} Classes</p>
            </div>
        </div>
        
        <div class="card" style="padding: 1.1rem 1.25rem; border: none; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 0.85rem; border-radius: 12px;">
            <div style="background: #f5f3ff; color: #8b5cf6; padding: 0.7rem; border-radius: 10px;">
                <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
            </div>
            <div>
                <h3 style="color: #64748b; margin: 0; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.025em;">Total Units</h3>
                <p style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0;">{{ $currentEnrollment->courses->sum('units') }} / 21</p>
            </div>
        </div>
    </div>

    @if($currentEnrollment->courses->count() > 0)
        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1rem; font-weight: 700; color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
                <i data-lucide="list-checks" style="width: 18px; height: 18px;"></i>
                Enrolled Course List
            </h2>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($currentEnrollment->courses as $course)
                    @php
                        $prefix = substr($course->course_code, 0, 2);
                        $theme = match($prefix) {
                            'CS' => ['icon' => 'code-2', 'color' => '#2563eb', 'bg' => '#eff6ff'],
                            'MATH' => ['icon' => 'variable', 'color' => '#db2777', 'bg' => '#fdf2f8'],
                            'PHYS' => ['icon' => 'atom', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                            'ENGL' => ['icon' => 'languages', 'color' => '#ea580c', 'bg' => '#fff7ed'],
                            default => ['icon' => 'book', 'color' => '#4b5563', 'bg' => '#f8fafc'],
                        };
                    @endphp
                    <div class="card" style="padding: 1rem; border: 1px solid #e2e8f0; border-radius: 14px; background: white; display: grid; grid-template-columns: 80px 1.5fr 1fr 1fr; align-items: center; gap: 1.5rem;">
                        <!-- Subject Badge -->
                        <div style="text-align: center;">
                            <div style="width: 40px; height: 40px; background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.25rem;">
                                <i data-lucide="{{ $theme['icon'] }}" style="width: 20px; height: 20px;"></i>
                            </div>
                            <span style="font-weight: 800; color: {{ $theme['color'] }}; font-size: 0.8rem;">{{ $course->course_code }}</span>
                        </div>

                        <!-- Title & Units -->
                        <div>
                            <h4 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">{{ $course->title }}</h4>
                            <p style="font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 4px;">
                                <i data-lucide="layers-2" style="width: 14px; height: 14px; opacity: 0.7;"></i>
                                {{ $course->units }} Units
                            </p>
                        </div>

                        <!-- Schedule Day & Time -->
                        <div style="background: #f8fafc; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 6px; color: #1e293b; font-weight: 700; font-size: 0.9rem; margin-bottom: 4px;">
                                <i data-lucide="calendar" style="width: 16px; height: 16px; color: #6366f1;"></i>
                                {{ $course->pivot->schedule_day ?? 'TBA' }}
                            </div>
                            <p style="font-size: 0.8rem; color: #64748b; font-weight: 600;">
                                {{ $course->pivot->start_time ? date('g:i A', strtotime($course->pivot->start_time)) : 'TBA' }} - 
                                {{ $course->pivot->end_time ? date('g:i A', strtotime($course->pivot->end_time)) : 'TBA' }}
                            </p>
                        </div>

                        <!-- Room & Prof -->
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px; color: #4b5563; font-weight: 600; font-size: 0.9rem; margin-bottom: 4px;">
                                <i data-lucide="map-pin" style="width: 16px; height: 16px; color: #94a3b8;"></i>
                                Room: {{ $course->pivot->room ?? 'TBA' }}
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 0.85rem;">
                                <i data-lucide="user" style="width: 16px; height: 16px; color: #94a3b8;"></i>
                                {{ $course->pivot->instructor ?? 'TBA' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.1rem; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                    <i data-lucide="calendar-days" style="width: 20px; height: 20px;"></i>
                    Weekly Timeline
                </h2>
            </div>
            
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 2rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);">
                @php
                    $weeklySchedule = [];
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    
                    foreach($currentEnrollment->courses as $course) {
                        if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time) {
                            $prefix = substr($course->course_code, 0, 2);
                            $colorData = match($prefix) {
                                'CS' => ['primary' => '#2563eb', 'bg' => '#eff6ff'],
                                'MATH' => ['primary' => '#db2777', 'bg' => '#fdf2f8'],
                                'PHYS' => ['primary' => '#7c3aed', 'bg' => '#f5f3ff'],
                                'ENGL' => ['primary' => '#ea580c', 'bg' => '#fff7ed'],
                                default => ['primary' => '#4b5563', 'bg' => '#f8fafc'],
                            };
                            $weeklySchedule[$course->pivot->schedule_day][] = [
                                'course' => $course,
                                'start_time' => $course->pivot->start_time,
                                'end_time' => $course->pivot->end_time,
                                'room' => $course->pivot->room,
                                'theme' => $colorData
                            ];
                        }
                    }
                @endphp
                
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem;">
                    @foreach($days as $day)
                        <div style="border-right: {{ !$loop->last ? '1px solid #f1f5f9' : 'none' }}; padding-right: {{ !$loop->last ? '1rem' : '0' }};">
                            <h4 style="color: #94a3b8; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1.5rem; text-align: center;">
                                {{ substr($day, 0, 3) }}
                            </h4>
                            
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @if(isset($weeklySchedule[$day]) && count($weeklySchedule[$day]) > 0)
                                    @php
                                        usort($weeklySchedule[$day], fn($a, $b) => strtotime($a['start_time']) - strtotime($b['start_time']));
                                    @endphp
                                    @foreach($weeklySchedule[$day] as $schedule)
                                        <div style="background: {{ $schedule['theme']['bg'] }}; border-radius: 12px; padding: 0.85rem; border: 1px solid rgba(0,0,0,0.02); transition: all 0.2s ease; cursor: default;" onmouseover="this.style.transform='scale(1.02)';" onmouseout="this.style.transform='scale(1)';">
                                            <div style="font-weight: 800; color: {{ $schedule['theme']['primary'] }}; font-size: 0.8rem; margin-bottom: 4px;">
                                                {{ $schedule['course']->course_code }}
                                            </div>
                                            <div style="font-size: 0.7rem; color: #1e293b; font-weight: 700; margin-bottom: 4px;">
                                                {{ date('g:i A', strtotime($schedule['start_time'])) }}
                                            </div>
                                            <div style="font-size: 0.65rem; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 3px;">
                                                <i data-lucide="map-pin" style="width: 10px; height: 10px;"></i>
                                                {{ $schedule['room'] ?? 'TBA' }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="height: 60px; border-radius: 12px; background: #fafafa; display: flex; align-items: center; justify-content: center; opacity: 0.5;">
                                        <div style="width: 4px; height: 4px; background: #cbd5e1; border-radius: 50%;"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>     </div>

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