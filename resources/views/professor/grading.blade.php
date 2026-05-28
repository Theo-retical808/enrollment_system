@extends('layouts.professor')

@section('title', 'Grading')

@section('content')
<div class="page-header mb-8">
    <h1 class="text-main font-extrabold" style="font-size: 2.2rem; margin-bottom: 0.25rem;">Student Grading</h1>
    <p class="text-muted font-bold">Submit grades for students enrolled in your courses</p>
</div>

@if($courseGroups->count() > 0)
    @foreach($courseGroups as $courseCode => $students)
    <div class="card mb-8" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
        <div style="padding: 1rem 1.5rem; background: var(--bg-primary); border-bottom: 2px solid var(--border-light); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 class="text-main font-extrabold" style="font-size: 1.1rem;">{{ $courseCode }}</h3>
                <span class="text-muted" style="font-size: 0.8rem;">{{ $students->first()->course_title }}</span>
            </div>
            <span class="badge badge-info" style="font-size: 0.8rem; padding: 4px 12px;">{{ $students->count() }} students</span>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <th style="padding: 0.75rem 1.5rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Student ID</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Name</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Current Grade</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Status</th>
                        <th style="padding: 0.75rem 1.5rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 0.75rem 1.5rem;">
                            <span class="text-main font-bold" style="font-size: 0.85rem;">{{ $student->student_id }}</span>
                        </td>
                        <td style="padding: 0.75rem 1rem;">
                            <span class="text-main font-bold">{{ $student->last_name }}, {{ $student->first_name }}</span>
                        </td>
                        <td style="padding: 0.75rem 1rem;">
                            @if($student->grade_status === 'graded')
                                <span style="font-weight: 700; color: {{ $student->numeric_grade <= 3.0 ? 'var(--status-success-text)' : 'var(--status-danger-text)' }};">
                                    {{ $student->numeric_grade }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 0.8rem;">—</span>
                            @endif
                        </td>
                        <td style="padding: 0.75rem 1rem;">
                            @if($student->grade_status === 'graded')
                                @if($student->numeric_grade <= 3.0)
                                    <span class="badge badge-success">Passed</span>
                                @else
                                    <span class="badge badge-danger">Failed</span>
                                @endif
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td style="padding: 0.75rem 1.5rem;">
                            <form method="POST" action="{{ route('professor.grading.submit') }}" style="display: flex; align-items: center; gap: 0.5rem;">
                                @csrf
                                <input type="hidden" name="enrollment_course_id" value="{{ $student->enrollment_course_id }}">
                                <input type="number" name="numeric_grade" step="0.25" min="1.0" max="5.0" value="{{ $student->numeric_grade }}" placeholder="1.0 - 5.0" required
                                    style="width: 80px; padding: 0.4rem 0.5rem; border-radius: 6px; border: 2px solid var(--border-light); background: var(--bg-primary); color: var(--text-main); font-size: 0.85rem; text-align: center;">
                                <button type="submit" style="padding: 0.4rem 0.75rem; border-radius: 6px; border: none; background: var(--udd-blue); color: white; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
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
<div class="card" style="text-align: center; padding: 4rem 2rem;">
    <i data-lucide="clipboard-list" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem; display: block;"></i>
    <h3 class="text-main font-extrabold" style="font-size: 1.25rem; margin-bottom: 0.5rem;">No Students to Grade</h3>
    <p class="text-muted">No approved enrollments found for your courses yet.</p>
</div>
@endif

<div style="margin-top: 2rem; padding: 1rem 1.5rem; background: var(--bg-primary); border-radius: 12px; border: 1px solid var(--border-light);">
    <h4 class="text-main font-bold" style="font-size: 0.85rem; margin-bottom: 0.5rem;">Grading Scale</h4>
    <p class="text-muted" style="font-size: 0.8rem;">
        <strong>1.0</strong> (Excellent) — <strong>1.75</strong> (Very Good) — <strong>2.0</strong> (Good) — <strong>2.5</strong> (Satisfactory) — <strong>3.0</strong> (Passing) — <strong>5.0</strong> (Failed)
        &nbsp;&nbsp;|&nbsp;&nbsp; Passing: 1.0 – 3.0 &nbsp;|&nbsp; Failed: 3.1 – 5.0
    </p>
</div>
@endsection
