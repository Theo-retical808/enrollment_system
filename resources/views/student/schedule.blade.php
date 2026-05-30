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

        /* Status Light Backgrounds */
        --success-bg-light: linear-gradient(to right, #ecfdf5, #d1fae5);
        --danger-bg-light: linear-gradient(to right, #fef2f2, #fee2e2);
        --warning-bg-light: linear-gradient(to right, #fffbeb, #fef3c7);
        --draft-bg-light: linear-gradient(to right, #f8fafc, #f1f5f9);
        
        --icon-bg-solid: white;

        /* Card Light Backgrounds */
        --semester-bg: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        --courses-bg: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
        --units-bg: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);

        --timeline-empty: rgba(0,0,0,0.02);
        --timeline-card-bg-gradient: linear-gradient(135deg, var(--card-bg) 0%, var(--bg-main) 100%);
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
        
        /* Status Dark Backgrounds */
        --success-bg-light: rgba(16, 185, 129, 0.1);
        --danger-bg-light: rgba(239, 68, 68, 0.1);
        --warning-bg-light: rgba(245, 158, 11, 0.1);
        --draft-bg-light: rgba(100, 116, 139, 0.1);

        --icon-bg-solid: #0f172a;

        /* Card Dark Backgrounds */
        --semester-bg: rgba(59, 130, 246, 0.1);
        --courses-bg: rgba(236, 72, 153, 0.1);
        --units-bg: rgba(139, 92, 246, 0.1);

        --timeline-empty: rgba(255,255,255,0.02);
        --timeline-card-bg-gradient: var(--card-bg);
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
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
        <div>
            <h1 style="color: var(--text-main); font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                @if($currentEnrollment && $currentEnrollment->status === 'approved')
                    My Approved Schedule
                @elseif($currentEnrollment && $currentEnrollment->status === 'submitted')
                    Schedule Under Review
                @else
                    Current Schedule (Draft)
                @endif
            </h1>
            <p style="color: var(--text-muted); font-size: 1.05rem; margin: 0; font-weight: 500;">View and manage your enrolled courses.</p>
        </div>
        
        @if($currentEnrollment && $currentEnrollment->status === 'approved')
            <div style="display: flex; gap: 0.75rem;" class="no-print">
                <button onclick="window.print()" class="btn" style="background: var(--card-bg); border: 1px solid var(--border-light); color: var(--text-main); font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                    <i data-lucide="printer" style="width: 18px; margin-right: 6px;"></i> Print
                </button>
                <a href="{{ route('student.schedule.export.pdf') }}" target="_blank" class="btn" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; font-weight: 700; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(59, 130, 246, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)';">
                    <i data-lucide="download" style="width: 18px; margin-right: 6px;"></i> Export PDF
                </a>
            </div>
        @endif
    </div>

    @if($currentEnrollment)
        <div style="margin-bottom: 2.5rem; padding: 1.5rem 2rem; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid var(--border-light); background: {{ $currentEnrollment->status === 'approved' ? 'var(--success-bg-light)' : ($currentEnrollment->status === 'submitted' ? 'var(--warning-bg-light)' : ($currentEnrollment->status === 'rejected' ? 'var(--danger-bg-light)' : 'var(--draft-bg-light)')) }};">
            
            @if($currentEnrollment->status === 'approved')
                <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                    <div style="background: var(--icon-bg-solid); color: #10b981; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);">
                        <i data-lucide="check-circle-2" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <p style="color: #065f46; margin: 0; font-weight: 800; font-size: 1.15rem;">Enrollment Approved</p>
                        <p style="color: #047857; margin: 0.25rem 0 0 0; font-size: 0.95rem; font-weight: 500;">This is your official schedule for {{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}.</p>
                    </div>
                </div>

            @elseif($currentEnrollment->status === 'rejected')
                <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                    <div style="background: var(--icon-bg-solid); color: #ef4444; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);">
                        <i data-lucide="alert-circle" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <p style="color: #991b1b; margin: 0; font-weight: 800; font-size: 1.15rem;">Schedule Rejected</p>
                        <p style="color: #b91c1c; margin: 0.25rem 0 0 0; font-size: 0.95rem; font-weight: 500;">Please review the comments below and resubmit.</p>
                        <div style="margin-top: 1.25rem;">
                            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: #ef4444; color: white; border: none; font-weight: 700; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);">
                                Revise and Resubmit
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($currentEnrollment->status === 'submitted')
                <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                    <div style="background: var(--icon-bg-solid); color: #f59e0b; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);">
                        <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <p style="color: #92400e; margin: 0; font-weight: 800; font-size: 1.15rem;">Under Review</p>
                        <p style="color: #b45309; margin: 0.25rem 0 0 0; font-size: 0.95rem; font-weight: 500;">Your schedule has been submitted and is currently under review by your professor.</p>
                    </div>
                </div>

            @else
                <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                    <div style="background: var(--icon-bg-solid); color: #64748b; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(100, 116, 139, 0.2);">
                        <i data-lucide="file-edit" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <p style="color: var(--text-main); margin: 0; font-weight: 800; font-size: 1.15rem;">Draft Schedule</p>
                        <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.95rem; font-weight: 500;">Continue your enrollment to submit this schedule for approval.</p>
                        <div style="margin-top: 1.25rem;">
                            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: #3b82f6; color: white; border: none; font-weight: 700; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);">
                                Continue Enrollment
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 3rem;">
            <!-- Semester Card -->
            <div style="padding: 1.5rem; border: 1px solid var(--border-light); background: var(--semester-bg); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 1.25rem; border-radius: 20px; position: relative; overflow: hidden; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="background: var(--icon-bg-solid); color: #3b82f6; padding: 1rem; border-radius: 14px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 1;">
                    <i data-lucide="graduation-cap" style="width: 28px; height: 28px;"></i>
                </div>
                <div style="z-index: 1;">
                    <h3 style="color: #3b82f6; margin: 0; font-size: 0.8rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Semester</h3>
                    <p style="font-size: 1.4rem; font-weight: 900; color: var(--text-main); margin: 0;">{{ $currentEnrollment->semester }}</p>
                </div>
            </div>
            
            <!-- Courses Card -->
            <div style="padding: 1.5rem; border: 1px solid var(--border-light); background: var(--courses-bg); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 1.25rem; border-radius: 20px; position: relative; overflow: hidden; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="background: var(--icon-bg-solid); color: #ec4899; padding: 1rem; border-radius: 14px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 1;">
                    <i data-lucide="book-open" style="width: 28px; height: 28px;"></i>
                </div>
                <div style="z-index: 1;">
                    <h3 style="color: #ec4899; margin: 0; font-size: 0.8rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Courses</h3>
                    <p style="font-size: 1.4rem; font-weight: 900; color: var(--text-main); margin: 0;">{{ $currentEnrollment->courses->count() }} Classes</p>
                </div>
            </div>
            
            <!-- Units Card -->
            <div style="padding: 1.5rem; border: 1px solid var(--border-light); background: var(--units-bg); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); display: flex; align-items: center; gap: 1.25rem; border-radius: 20px; position: relative; overflow: hidden; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
                <div style="background: var(--icon-bg-solid); color: #8b5cf6; padding: 1rem; border-radius: 14px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 1;">
                    <i data-lucide="layers" style="width: 28px; height: 28px;"></i>
                </div>
                <div style="z-index: 1;">
                    <h3 style="color: #8b5cf6; margin: 0; font-size: 0.8rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Total Units</h3>
                    <p style="font-size: 1.4rem; font-weight: 900; color: var(--text-main); margin: 0;">{{ $currentEnrollment->courses->sum('units') }} / 21</p>
                </div>
            </div>
        </div>

    @if($currentEnrollment->courses->count() > 0)
        <div style="margin-bottom: 3.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                    <div style="background: var(--blue-bg); padding: 6px; border-radius: 8px; color: var(--blue-text);">
                        <i data-lucide="list-checks" style="width: 20px; height: 20px;"></i>
                    </div>
                    Enrolled Course List
                </h2>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($currentEnrollment->courses as $course)
                    @php
                        $prefix = substr($course->course_code, 0, 2);
                        $theme = match($prefix) {
                            'CS' => ['icon' => 'code-2', 'color' => '#2563eb', 'bg' => 'rgba(37, 99, 235, 0.1)'],
                            'MATH' => ['icon' => 'variable', 'color' => '#db2777', 'bg' => 'rgba(219, 39, 119, 0.1)'],
                            'PHYS' => ['icon' => 'atom', 'color' => '#7c3aed', 'bg' => 'rgba(124, 58, 237, 0.1)'],
                            'ENGL' => ['icon' => 'languages', 'color' => '#ea580c', 'bg' => 'rgba(234, 88, 12, 0.1)'],
                            default => ['icon' => 'book', 'color' => '#64748b', 'bg' => 'rgba(100, 116, 139, 0.1)'],
                        };
                    @endphp
                    <div style="padding: 1.5rem; border: 1px solid var(--border-light); border-radius: 16px; background: var(--card-bg); display: grid; grid-template-columns: 80px 1.5fr 1fr 1fr; align-items: center; gap: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 30px -5px rgba(0, 0, 0, 0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px -5px rgba(0, 0, 0, 0.05)';">
                        <!-- Subject Badge -->
                        <div style="text-align: center;">
                            <div style="width: 48px; height: 48px; background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.35rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                <i data-lucide="{{ $theme['icon'] }}" style="width: 24px; height: 24px;"></i>
                            </div>
                            <span style="font-weight: 800; color: {{ $theme['color'] }}; font-size: 0.85rem;">{{ $course->course_code }}</span>
                        </div>

                        <!-- Title & Units -->
                        <div>
                            <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">{{ $course->title }}</h4>
                            <span style="display: inline-flex; align-items: center; gap: 6px; background: var(--border-light); color: var(--text-main); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                                <i data-lucide="layers-2" style="width: 14px; height: 14px; color: var(--text-muted);"></i>
                                {{ $course->units }} Units
                            </span>
                        </div>

                        <!-- Schedule Day & Time -->
                        <div style="background: var(--timeline-card-bg-gradient); padding: 1rem 1.25rem; border-radius: 14px; border: 1px solid var(--border-light);">
                            <div style="display: flex; align-items: center; gap: 8px; color: var(--text-main); font-weight: 800; font-size: 0.95rem; margin-bottom: 6px;">
                                <i data-lucide="calendar" style="width: 18px; height: 18px; color: {{ $theme['color'] }};"></i>
                                {{ $course->pivot->schedule_day ?? 'TBA' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                                {{ $course->pivot->start_time ? date('g:i A', strtotime($course->pivot->start_time)) : 'TBA' }} - 
                                {{ $course->pivot->end_time ? date('g:i A', strtotime($course->pivot->end_time)) : 'TBA' }}
                            </div>
                        </div>

                        <!-- Room & Prof -->
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 10px; color: var(--text-main); font-weight: 700; font-size: 0.95rem;">
                                <div style="background: var(--bg-main); padding: 6px; border-radius: 8px;">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                                </div>
                                {{ $course->pivot->room ?? 'TBA' }}
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-weight: 600; font-size: 0.9rem;">
                                <div style="background: var(--bg-main); padding: 6px; border-radius: 8px;">
                                    <i data-lucide="user" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                                </div>
                                {{ $course->pivot->instructor ?? 'TBA' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="margin-bottom: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                    <div style="background: var(--blue-bg); padding: 6px; border-radius: 8px; color: var(--blue-text);">
                        <i data-lucide="calendar-days" style="width: 20px; height: 20px;"></i>
                    </div>
                    Weekly Timeline
                </h2>
            </div>
            
            <div style="background: var(--timeline-card-bg-gradient); border: 1px solid var(--border-light); border-radius: 24px; padding: 2.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);">
                @php
                    $weeklySchedule = [];
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    
                    foreach($currentEnrollment->courses as $course) {
                        if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time) {
                            $prefix = substr($course->course_code, 0, 2);
                            $colorData = match($prefix) {
                                'CS' => ['primary' => '#2563eb', 'bg' => 'rgba(37, 99, 235, 0.1)'],
                                'MATH' => ['primary' => '#db2777', 'bg' => 'rgba(219, 39, 119, 0.1)'],
                                'PHYS' => ['primary' => '#7c3aed', 'bg' => 'rgba(124, 58, 237, 0.1)'],
                                'ENGL' => ['primary' => '#ea580c', 'bg' => 'rgba(234, 88, 12, 0.1)'],
                                default => ['primary' => '#4b5563', 'bg' => 'rgba(100, 116, 139, 0.1)'],
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
                
                <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.5rem;">
                    @foreach($days as $day)
                        <div style="border-right: {{ !$loop->last ? '2px dashed var(--border-light)' : 'none' }}; padding-right: {{ !$loop->last ? '1.5rem' : '0' }};">
                            <h4 style="color: var(--text-muted); font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 2rem; text-align: center; position: relative;">
                                <span style="background: var(--card-bg); padding: 0.5rem 1rem; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid var(--border-light);">
                                    {{ substr($day, 0, 3) }}
                                </span>
                            </h4>
                            
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                @if(isset($weeklySchedule[$day]) && count($weeklySchedule[$day]) > 0)
                                    @php
                                        usort($weeklySchedule[$day], fn($a, $b) => strtotime($a['start_time']) - strtotime($b['start_time']));
                                    @endphp
                                    @foreach($weeklySchedule[$day] as $schedule)
                                        <div style="background: {{ $schedule['theme']['bg'] }}; border-radius: 14px; padding: 1.1rem; border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: default;" onmouseover="this.style.transform='translateY(-4px) scale(1.02)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.05)';">
                                            <div style="font-weight: 900; color: {{ $schedule['theme']['primary'] }}; font-size: 0.9rem; margin-bottom: 6px;">
                                                {{ $schedule['course']->course_code }}
                                            </div>
                                            <div style="font-size: 0.8rem; color: var(--text-main); font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 4px;">
                                                <i data-lucide="clock" style="width: 12px; height: 12px; opacity: 0.7;"></i>
                                                {{ date('g:i A', strtotime($schedule['start_time'])) }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; display: flex; align-items: center; gap: 4px; background: rgba(0,0,0,0.05); padding: 4px 8px; border-radius: 8px; width: fit-content;">
                                                <i data-lucide="map-pin" style="width: 12px; height: 12px;"></i>
                                                {{ $schedule['room'] ?? 'TBA' }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="height: 80px; border-radius: 14px; background: var(--timeline-empty); border: 2px dashed var(--border-light); display: flex; align-items: center; justify-content: center; opacity: 0.6;">
                                        <i data-lucide="coffee" style="width: 24px; height: 24px; color: var(--text-light);"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>     

        @else
            <div class="card" style="text-align: center; padding: 5rem 2rem; background: var(--timeline-card-bg-gradient); border: 1px solid var(--border-light); border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);">
                <div style="background: var(--icon-bg-solid); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                    <svg style="width: 40px; height: 40px; color: var(--text-light);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <h3 style="color: var(--text-main); font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">No Courses Enrolled</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.05rem;">You haven't selected any courses for the upcoming semester yet.</p>
                
                @if($student->isRegular())
                    <a href="{{ route('student.enrollment.regular') }}" class="btn" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; font-size: 1.1rem; padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">Get Assigned Schedule</a>
                @else
                    <a href="{{ route('student.enrollment.irregular') }}" class="btn" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; font-size: 1.1rem; padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">Select Courses</a>
                @endif
            </div>
        @endif
        
    @else
        <div class="card" style="text-align: center; padding: 5rem 2rem; background: var(--timeline-card-bg-gradient); border: 1px solid var(--border-light); border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);">
            <div style="background: var(--icon-bg-solid); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                <svg style="width: 40px; height: 40px; color: var(--text-light);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 style="color: var(--text-main); font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">No Active Enrollment</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.05rem;">You do not have a current enrollment record. Start your enrollment process now.</p>
            
            <a href="{{ $student->isRegular() ? route('student.enrollment.regular') : route('student.enrollment.irregular') }}" class="btn" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; font-size: 1.1rem; padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                Start Enrollment
            </a>
        </div>
    @endif
</div>
@endsection