@extends('layouts.professor')

@section('title', 'Grading')

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

        --status-pending-bg: #fffbeb;
        --status-pending-border: #fef3c7;
        --status-pending-text: #92400e;

        --status-default-bg: #f8fafc;
        --status-default-border: #f1f5f9;
        --status-default-text: #0f172a;
    }

    /* Dark Mode specific mappings could be added here if needed, 
       but keeping simple matching student layout */
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">Student Grading</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Submit grades for students enrolled in your courses</p>
    </div>

    @if($courseGroups->count() > 0)
        @foreach($courseGroups as $courseCode => $students)
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden; margin-bottom: 2rem;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--card-border); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main); margin: 0;">{{ $courseCode }}</h2>
                    <span style="color: var(--text-muted); font-size: 0.85rem;">{{ $students->first()->course_title }}</span>
                </div>
                <span style="background: var(--status-enrolled-bg, #f0f9ff); color: var(--status-enrolled-text, #075985); padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                    {{ $students->count() }} Students
                </span>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: var(--card-bg);">
                        <tr>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Student ID</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Name</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Current Grade</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Status</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr style="border-bottom: 1px solid var(--card-border);">
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">{{ $student->student_id }}</td>
                            <td style="padding: 1rem; color: var(--text-main); font-weight: 500;">{{ $student->last_name }}, {{ $student->first_name }}</td>
                            <td style="padding: 1rem;">
                                @if($student->grade_status === 'graded')
                                    <span style="font-weight: 700; color: {{ $student->numeric_grade <= 3.0 ? 'var(--status-success-text)' : 'var(--status-failed-text)' }};">
                                        {{ number_format((float)$student->numeric_grade, 2, '.', '') }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">—</span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                @if($student->grade_status === 'graded')
                                    @if($student->numeric_grade <= 3.0)
                                        <span style="background: var(--status-success-bg); color: var(--status-success-text); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                            ✓ Passed
                                        </span>
                                    @else
                                        <span style="background: var(--status-failed-bg); color: var(--status-failed-text); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                            ✗ Failed
                                        </span>
                                    @endif
                                @else
                                    <span style="background: var(--status-pending-bg); color: var(--status-pending-text); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem;">
                                <form method="POST" action="{{ route('professor.grading.submit') }}" style="display: flex; align-items: center; gap: 0.75rem;">
                                    @csrf
                                    <input type="hidden" name="enrollment_course_id" value="{{ $student->enrollment_course_id }}">
                                    <input type="number" name="numeric_grade" step="0.25" min="1.0" max="5.0" value="{{ $student->numeric_grade }}" placeholder="1.0 - 5.0" required
                                        style="width: 85px; padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--card-border); background: var(--bg-white); color: var(--text-main); font-size: 0.875rem; text-align: center; outline: none;"
                                        onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='var(--card-border)'">
                                    <button type="submit" style="padding: 0.5rem 1rem; border-radius: 8px; border: none; background: #3b82f6; color: white; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#2563eb'" onmouseout="this.style.background='#3b82f6'">
                                        {{ $student->grade_status === 'graded' ? 'Update' : 'Submit' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    @else
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 4rem 2rem; text-align: center;">
            <i data-lucide="clipboard-list" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem; display: block;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">No Students to Grade</h3>
            <p style="color: var(--text-muted);">No approved enrollments found for your courses yet.</p>
        </div>
    @endif

    <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-white); border-radius: 16px; border: 1px solid var(--card-border);">
        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">Grading Scale</h4>
        <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.6;">
            <strong style="color: var(--text-main);">1.0</strong> (Excellent) — <strong style="color: var(--text-main);">1.75</strong> (Very Good) — <strong style="color: var(--text-main);">2.0</strong> (Good) — <strong style="color: var(--text-main);">2.5</strong> (Satisfactory) — <strong style="color: var(--text-main);">3.0</strong> (Passing) — <strong style="color: var(--status-failed-text);">5.0</strong> (Failed)<br>
            <span style="color: var(--status-success-text); font-weight: 600;">Passing: 1.0 – 3.0</span> &nbsp;|&nbsp; <span style="color: var(--status-failed-text); font-weight: 600;">Failed: 3.1 – 5.0</span>
        </p>
    </div>
</div>
@endsection
