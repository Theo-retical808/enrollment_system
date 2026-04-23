@extends('layouts.student')

@section('title', 'My Courses')

@section('content')
<style>
    /* Map local variables to global theme variables for consistent theming */
    :root {
        --text-main: var(--text-primary);
        --text-muted: var(--text-secondary);
        --card-bg: var(--bg-white);
        --card-border: var(--border-light);
        --grad-start: var(--accent-blue);
        --grad-end: var(--accent-blue-text);
        /* Status / state colors (light defaults) */
        --status-success-bg: #f0fdf4;
        --status-success-border: #dcfce7;
        --status-success-text: #166534;

        --status-failed-bg: #fef2f2;
        --status-failed-border: #fee2e2;
        --status-failed-text: #991b1b;

        --status-enrolled-bg: #f0f9ff;
        --status-enrolled-border: #bae6fd;
        --status-enrolled-text: #075985;

        --status-default-bg: #f8fafc;
        --status-default-border: #f1f5f9;
        --status-default-text: #0f172a;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">My Courses</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">View your curriculum and track your academic progress</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding:1.1rem 1.25rem; display: flex; align-items: center; gap: 0.85rem; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border-radius: 12px;">
            <div style="background: #ecfdf5; color: #10b981; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="check-circle" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem; font-weight: 600;">Completed Courses</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main);">{{ $completedCount }}</div>
            </div>
        </div>

        <div class="card" style="padding:1.1rem 1.25rem; display: flex; align-items: center; gap: 0.85rem; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border-radius: 12px;">
            <div style="background: #fef2f2; color: #ef4444; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="book-open" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem; font-weight: 600;">Units Completed</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main);">{{ $totalUnitsCompleted }}</div>
            </div>
        </div>

        <div class="card" style="padding:1.5rem; display: flex; align-items: center; gap: 1rem; border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #eff6ff; color: #3b82f6; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem; font-weight: 600;">Current Enrollment</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main);">{{ $currentCourses->count() }}</div>
            </div>
        </div>
    </div>

    @if($currentCourses->count() > 0)
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="layout-grid" style="color: #6366f1; width: 20px; height: 20px;"></i>
            Currently Enrolled
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
            @foreach($currentCourses as $course)
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
                <div class="card" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 14px; transition: all 0.3s ease; background: white;" onmouseover="this.style.borderColor='{{ $theme['color'] }}'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)';">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; background: {{ $theme['bg'] }}; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: {{ $theme['color'] }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <i data-lucide="{{ $theme['icon'] }}" style="width: 16px; height: 16px;"></i>
                            </div>
                            <span style="font-weight: 800; color: {{ $theme['color'] }}; font-size: 0.9rem;">{{ $course->course_code }}</span>
                        </div>
                        <span style="font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Yr {{ $course->year_level }}</span>
                    </div>
                    <div style="padding: 1rem 1.25rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; line-height: 1.4; min-height: 2.8rem;">{{ $course->title }}</h3>
                        <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 0.8rem; font-weight: 500;">
                            <i data-lucide="layers-2" style="width: 14px; height: 14px; opacity: 0.7;"></i>
                            {{ $course->units }} Units
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($completedCourses->count() > 0)
    <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--card-border);">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main); margin: 0;">Completed Courses</h2>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--card-bg);">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Course Code</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Course Title</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Units</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Grade</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Status</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($completedCourses->sortByDesc('pivot.academic_year') as $course)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem; font-weight: 600; color: #0f172a;">{{ $course->course_code }}</td>
                            <td style="padding: 1rem; color: #475569;">{{ $course->title }}</td>
                            <td style="padding: 1rem; color: #475569;">{{ $course->units }}</td>
                            <td style="padding: 1rem;">
                                <span style="font-weight: 700; color: {{ $course->pivot->passed ? '#059669' : '#dc2626' }};">
                                    {{ $course->pivot->grade }}
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                @if($course->pivot->passed)
                                    <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ✓ Passed
                                    </span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ✗ Failed
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem; color: #64748b; font-size: 0.875rem;">
                                {{ $course->pivot->semester }} {{ $course->pivot->academic_year }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0;">
                {{ $student->school->name }} Curriculum
            </h2>
        </div>
        <div style="padding: 1.5rem;">
            @foreach($allCourses as $yearLevel => $courses)
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #2563eb; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #dbeafe;">
                        Year {{ $yearLevel }}
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                        @foreach($courses as $course)
                            @php
                                $isCompleted = $completedCourses->where('id', $course->id)->where('pivot.passed', true)->isNotEmpty();
                                $isFailed = $completedCourses->where('id', $course->id)->where('pivot.passed', false)->isNotEmpty();
                                $isEnrolled = $currentCourses->where('id', $course->id)->isNotEmpty();
                            @endphp
                            <div style="background: {{ $isCompleted ? 'var(--status-success-bg)' : ($isFailed ? 'var(--status-failed-bg)' : ($isEnrolled ? 'var(--status-enrolled-bg)' : 'var(--status-default-bg)')) }}; 
                                        border: 1px solid {{ $isCompleted ? 'var(--status-success-border)' : ($isFailed ? 'var(--status-failed-border)' : ($isEnrolled ? 'var(--status-enrolled-border)' : 'var(--status-default-border)')) }}; 
                                        border-radius: 12px; padding: 1rem; position: relative;">
                                @if($isCompleted)
                                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: var(--status-success-border); color: var(--status-success-text); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($isFailed)
                                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: var(--status-failed-border); color: var(--status-failed-text); width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($isEnrolled)
                                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: var(--status-enrolled-border); color: var(--status-enrolled-text); padding: 0.25rem 0.5rem; border-radius: 8px; font-size: 0.625rem; font-weight: 700;">
                                        ENROLLED
                                    </div>
                                @endif
                                
                                <div style="font-weight: 700; color: {{ $isCompleted ? 'var(--status-success-text)' : ($isFailed ? 'var(--status-failed-text)' : ($isEnrolled ? 'var(--status-enrolled-text)' : 'var(--status-default-text)')) }}; margin-bottom: 0.25rem;">
                                    {{ $course->course_code }}
                                </div>
                                <div style="color: {{ $isCompleted ? 'var(--status-success-text)' : ($isFailed ? 'var(--status-failed-text)' : ($isEnrolled ? 'var(--status-enrolled-text)' : 'var(--text-muted)')) }}; font-size: 0.875rem; margin-bottom: 0.5rem; line-height: 1.4;">
                                    {{ $course->title }}
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="background: {{ $isCompleted ? 'var(--status-success-border)' : ($isFailed ? 'var(--status-failed-border)' : ($isEnrolled ? 'var(--status-enrolled-border)' : 'var(--status-default-border)')) }}; 
                                                color: {{ $isCompleted ? 'var(--status-success-text)' : ($isFailed ? 'var(--status-failed-text)' : ($isEnrolled ? 'var(--status-enrolled-text)' : 'var(--text-muted)')) }}; 
                                                padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                        {{ $course->units }} units
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection