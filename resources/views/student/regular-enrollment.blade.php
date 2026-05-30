@extends('layouts.student')

@section('title', 'Regular Enrollment')

@section('content')
<style>
    :root {
        --text-main: var(--text-primary);
        --text-muted: var(--text-secondary);
        --card-bg: var(--bg-white);
        --card-border: var(--border-light);
        --bg-main: var(--primary-bg);
        
        --grad-start: var(--accent-blue);
        --grad-end: var(--accent-blue-text);
        --blue-bg: var(--accent-blue);
        --blue-text: var(--accent-blue-text);
    }
</style>

<div style="max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 2.5rem;">
        <h1 style="color: var(--text-main); font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
            Regular Student Enrollment
        </h1>
        <p style="color: var(--text-muted); font-size: 1.05rem; margin: 0; font-weight: 500;">
            Your schedule has been automatically assigned based on your program curriculum
        </p>
    </div>

    @if(session('success'))
        <div style="background: linear-gradient(to right, #ecfdf5, #d1fae5); border: 1px solid #10b981; border-radius: 12px; padding: 1rem 1.5rem; color: #065f46; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(16,185,129,0.1);">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: linear-gradient(to right, #fef2f2, #fee2e2); border: 1px solid #ef4444; border-radius: 12px; padding: 1rem 1.5rem; color: #991b1b; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(239,68,68,0.1);">
            <i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Student & Summary Info Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        
        <!-- Enrollment Information -->
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; top: -20px; opacity: 0.03; transform: scale(5);">
                <i data-lucide="user"></i>
            </div>
            
            <h2 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em; z-index: 1; position: relative;">
                <div style="background: #eff6ff; color: #3b82f6; padding: 6px; border-radius: 8px;">
                    <i data-lucide="info" style="width: 18px; height: 18px;"></i>
                </div>
                Enrollment Information
            </h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; z-index: 1; position: relative;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Student Name</span>
                    <p style="font-weight: 800; color: var(--text-main); font-size: 1rem; margin: 0;">{{ $student->full_name }}</p>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Student ID</span>
                    <p style="font-weight: 800; color: var(--text-main); font-size: 1rem; margin: 0;">{{ $student->student_id }}</p>
                </div>
                <div style="grid-column: span 2;">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">School</span>
                    <p style="font-weight: 800; color: var(--text-main); font-size: 1rem; margin: 0;">{{ $student->school->name }}</p>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Year Level</span>
                    <p style="font-weight: 800; color: var(--text-main); font-size: 1rem; margin: 0;">{{ $student->year_level }}</p>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Term</span>
                    <p style="font-weight: 800; color: var(--text-main); font-size: 1rem; margin: 0;">{{ $enrollment->semester }} ({{ $enrollment->academic_year }})</p>
                </div>
            </div>
        </div>

        <!-- Schedule Summary -->
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; top: -20px; opacity: 0.03; transform: scale(5);">
                <i data-lucide="file-text"></i>
            </div>
            
            <h2 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em; z-index: 1; position: relative;">
                <div style="background: #fdf2f8; color: #ec4899; padding: 6px; border-radius: 8px;">
                    <i data-lucide="bar-chart-2" style="width: 18px; height: 18px;"></i>
                </div>
                Schedule Summary
            </h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; z-index: 1; position: relative;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Courses</span>
                    <p style="font-weight: 900; color: var(--text-main); font-size: 1.5rem; margin: 0;">{{ $enrollment->courses->count() }}</p>
                </div>
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Units</span>
                    <p style="font-weight: 900; color: var(--text-main); font-size: 1.5rem; margin: 0;">{{ $enrollment->total_units }} <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">/ 21</span></p>
                </div>
                <div style="grid-column: span 2; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-light); padding-top: 1.25rem; margin-top: 0.5rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Status</span>
                        <div style="margin-top: 0.25rem;">
                            @if($enrollment->status === 'approved')
                                <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="check" style="width: 12px;"></i> Approved</span>
                            @elseif($enrollment->status === 'submitted')
                                <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="clock" style="width: 12px;"></i> Submitted</span>
                            @elseif($enrollment->status === 'rejected')
                                <span style="background: #ef4444; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="x" style="width: 12px;"></i> Rejected</span>
                            @else
                                <span style="background: #64748b; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 4px;"><i data-lucide="edit-2" style="width: 12px;"></i> Draft</span>
                            @endif
                        </div>
                    </div>
                    @if($enrollment->professor)
                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Reviewer</span>
                            <p style="font-weight: 700; color: var(--text-main); font-size: 0.9rem; margin: 0;">{{ $enrollment->professor->full_name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Schedule -->
    <div style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 0.05em;">
            <div style="background: #f5f3ff; color: #8b5cf6; padding: 6px; border-radius: 8px;">
                <i data-lucide="calendar-days" style="width: 20px; height: 20px;"></i>
            </div>
            Your Assigned Schedule
        </h2>

        @if($enrollment->courses->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($enrollment->courses as $course)
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
                        <div style="background: linear-gradient(135deg, var(--bg-main) 0%, var(--card-bg) 100%); padding: 1rem 1.25rem; border-radius: 14px; border: 1px solid var(--border-light);">
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
        @else
            <div style="text-align: center; padding: 4rem 2rem; background: var(--card-bg); border: 1px solid var(--border-light); border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
                <i data-lucide="inbox" style="width: 48px; height: 48px; color: var(--text-light); margin: 0 auto 1rem;"></i>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">No courses assigned yet.</h3>
                <p style="color: var(--text-muted); font-weight: 500;">Please refresh the page or contact the registrar.</p>
            </div>
        @endif
    </div>

    <!-- Actions Box -->
    <div style="background: linear-gradient(135deg, var(--card-bg) 0%, var(--bg-main) 100%); border: 1px solid var(--border-light); border-radius: 20px; padding: 2rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
        
        @if($enrollment->status === 'draft')
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <form method="POST" action="{{ route('student.enrollment.regular.submit') }}">
                    @csrf
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; font-size: 1rem; padding: 0.85rem 2rem; border-radius: 12px; font-weight: 800; box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3); transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                        <i data-lucide="send" style="width: 18px; height: 18px;"></i>
                        Submit for Approval
                    </button>
                </form>
                
                <form method="POST" action="{{ route('student.enrollment.regular.reset') }}">
                    @csrf
                    <button type="submit" class="btn" style="background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border-light); font-size: 1rem; padding: 0.85rem 2rem; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onclick="return confirm('Are you sure you want to reset your enrollment? This will generate a new schedule.')" onmouseover="this.style.background='var(--hover-bg)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='var(--card-bg)'; this.style.transform='translateY(0)';">
                        <i data-lucide="rotate-ccw" style="width: 18px; height: 18px;"></i>
                        Reset Schedule
                    </button>
                </form>
            </div>

        @elseif($enrollment->status === 'submitted')
            <div style="text-align: center;">
                <div style="background: #fdf6e3; border: 1px solid #f59e0b; color: #b45309; border-radius: 12px; padding: 1.5rem; display: inline-block; text-align: left; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <i data-lucide="clock" style="width: 24px; height: 24px; color: #f59e0b;"></i>
                        <strong style="font-size: 1.1rem; color: #92400e;">Pending Approval</strong>
                    </div>
                    <p style="margin: 0; font-weight: 500;">Your enrollment is pending approval from <strong>{{ $enrollment->professor->full_name ?? 'your assigned professor' }}</strong>.<br>You will be notified once your schedule has been reviewed.</p>
                </div>
            </div>

        @elseif($enrollment->status === 'approved')
            <div style="text-align: center;">
                <div style="background: #ecfdf5; border: 1px solid #10b981; color: #047857; border-radius: 12px; padding: 1.5rem; display: inline-block; text-align: left; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <i data-lucide="check-circle" style="width: 24px; height: 24px; color: #10b981;"></i>
                        <strong style="font-size: 1.1rem; color: #065f46;">Enrollment Approved</strong>
                    </div>
                    <p style="margin: 0; font-weight: 500;">Your schedule is now finalized for this semester.</p>
                </div>
            </div>

        @elseif($enrollment->status === 'rejected')
            <div style="text-align: center;">
                <div style="background: #fef2f2; border: 1px solid #ef4444; color: #b91c1c; border-radius: 12px; padding: 1.5rem; display: inline-block; text-align: left; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <i data-lucide="x-circle" style="width: 24px; height: 24px; color: #ef4444;"></i>
                        <strong style="font-size: 1.1rem; color: #991b1b;">Enrollment Rejected</strong>
                    </div>
                    @if($enrollment->review_comments)
                        <p style="margin: 0; font-weight: 500;"><strong>Comments:</strong> {{ $enrollment->review_comments }}</p>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('student.enrollment.regular.reset') }}">
                    @csrf
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; border: none; font-size: 1rem; padding: 0.85rem 2rem; border-radius: 12px; font-weight: 800; box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                        Create New Enrollment
                    </button>
                </form>
            </div>
        @endif
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('student.dashboard') }}" class="btn" style="background: transparent; color: var(--text-muted); border: none; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)';" onmouseout="this.style.color='var(--text-muted)';">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
