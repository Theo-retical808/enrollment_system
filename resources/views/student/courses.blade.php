@extends('layouts.student')

@section('title', 'My Courses')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">My Courses</h1>
        <p style="color: #64748b; font-size: 0.95rem;">View your curriculum and track your academic progress</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border-radius: 16px; padding: 1.5rem; color: white;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Completed Courses</div>
                    <div style="font-size: 1.75rem; font-weight: 800;">{{ $completedCount }}</div>
                </div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #dbeafe; color: #2563eb; padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Units Completed</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">{{ $totalUnitsCompleted }}</div>
                </div>
            </div>
        </div>

        @if($failedCount > 0)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #fee2e2; color: #ef4444; padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Failed Courses</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">{{ $failedCount }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($currentCourses->count() > 0)
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #e0f2fe; color: #0284c7; padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Current Enrollment</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">{{ $currentCourses->count() }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($currentCourses->count() > 0)
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #e0f2fe; background: #f0f9ff;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #075985; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Currently Enrolled (This Semester)
            </h2>
        </div>
        <div style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach($currentCourses as $course)
                    <div style="background: #f8fafc; border: 1px solid #e0f2fe; border-radius: 12px; padding: 1rem;">
                        <div style="font-weight: 700; color: #075985; margin-bottom: 0.25rem;">{{ $course->course_code }}</div>
                        <div style="color: #0c4a6e; font-size: 0.875rem; margin-bottom: 0.5rem;">{{ $course->title }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="background: #e0f2fe; color: #0369a1; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                {{ $course->units }} units
                            </span>
                            <span style="color: #0c4a6e; font-size: 0.75rem;">Year {{ $course->year_level }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($completedCourses->count() > 0)
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0;">Completed Courses</h2>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Course Code</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Course Title</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Units</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Grade</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Status</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Semester</th>
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
                            <div style="background: {{ $isCompleted ? '#f0fdf4' : ($isFailed ? '#fef2f2' : ($isEnrolled ? '#f0f9ff' : '#f8fafc')) }}; 
                                        border: 1px solid {{ $isCompleted ? '#dcfce7' : ($isFailed ? '#fee2e2' : ($isEnrolled ? '#bae6fd' : '#f1f5f9')) }}; 
                                        border-radius: 12px; padding: 1rem; position: relative;">
                                @if($isCompleted)
                                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: #dcfce7; color: #166534; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($isFailed)
                                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: #fee2e2; color: #991b1b; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @elseif($isEnrolled)
                                    <div style="position: absolute; top: 0.5rem; right: 0.5rem; background: #e0f2fe; color: #0369a1; padding: 0.25rem 0.5rem; border-radius: 8px; font-size: 0.625rem; font-weight: 700;">
                                        ENROLLED
                                    </div>
                                @endif
                                
                                <div style="font-weight: 700; color: {{ $isCompleted ? '#166534' : ($isFailed ? '#991b1b' : ($isEnrolled ? '#075985' : '#0f172a')) }}; margin-bottom: 0.25rem;">
                                    {{ $course->course_code }}
                                </div>
                                <div style="color: {{ $isCompleted ? '#15803d' : ($isFailed ? '#b91c1c' : ($isEnrolled ? '#0c4a6e' : '#475569')) }}; font-size: 0.875rem; margin-bottom: 0.5rem; line-height: 1.4;">
                                    {{ $course->title }}
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="background: {{ $isCompleted ? '#dcfce7' : ($isFailed ? '#fee2e2' : ($isEnrolled ? '#e0f2fe' : '#e2e8f0')) }}; 
                                                color: {{ $isCompleted ? '#166534' : ($isFailed ? '#991b1b' : ($isEnrolled ? '#0369a1' : '#475569')) }}; 
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