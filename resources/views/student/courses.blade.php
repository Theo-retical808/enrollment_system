@extends('layouts.student')

@section('title', 'Academic Progress')

@section('content')

    <div class="page-header mb-8">
        <h1 class="text-main font-extrabold" style="font-size: 2.2rem; margin-bottom: 0.25rem;">My Courses</h1>
        <p class="text-muted font-bold">Track your curriculum and academic achievements</p>
    </div>

    <!-- Progress Overview -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card" style="background: var(--udd-blue); color: white; border: none;">
            <div class="flex items-center gap-4">
                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 12px;">
                    <i data-lucide="check-circle-2" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div
                        style="font-size: 0.85rem; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.05em;">
                        Completed</div>
                    <div style="font-size: 2rem; font-weight: 800; line-height: 1;">{{ $completedCount }} <span
                            style="font-size: 0.9rem; font-weight: 600;">Courses</span></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center gap-4">
                <div
                    style="background: var(--status-info-bg); color: var(--status-info-text); padding: 0.75rem; border-radius: 12px;">
                    <i data-lucide="award" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                        Total Credits</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-main); line-height: 1;">
                        {{ $totalUnitsCompleted }} <span style="font-size: 0.9rem; font-weight: 600;">Units</span>
                    </div>
                </div>
            </div>
        </div>

        @if($failedCount > 0)
            <div class="card">
                <div class="flex items-center gap-4">
                    <div
                        style="background: var(--status-danger-bg); color: var(--status-danger-text); padding: 0.75rem; border-radius: 12px;">
                        <i data-lucide="alert-circle" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                            Failed Load</div>
                        <div style="font-size: 2rem; font-weight: 800; color: var(--status-danger-text); line-height: 1;">
                            {{ $failedCount }} <span style="font-size: 0.9rem; font-weight: 600;">Courses</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="flex items-center gap-4">
                <div
                    style="background: var(--udd-blue-light); color: var(--udd-blue); padding: 0.75rem; border-radius: 12px;">
                    <i data-lucide="book-open" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                        In Progress</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-main); line-height: 1;">
                        {{ $currentCourses->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Semester -->
    @if($currentCourses->count() > 0)
        <div class="card mb-8" style="padding: 0; overflow: hidden; border-color: var(--udd-blue);">
            <div
                style="padding: 1.25rem 2rem; background: var(--udd-blue-light); border-bottom: 2px solid var(--udd-blue); display: flex; align-items: center; gap: 10px;">
                <i data-lucide="calendar-clock" style="color: var(--udd-blue); width: 20px;"></i>
                <h2
                    style="font-size: 1.1rem; font-weight: 800; color: var(--udd-blue); margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                    Active Enrollment
                </h2>
            </div>
            <div style="padding: 2rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
                    @foreach($currentCourses as $course)
                        <div style="background: var(--bg-primary); border: 2px solid var(--border-main); border-radius: 16px; padding: 1.25rem; transition: transform 0.2s;"
                            onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-extrabold"
                                    style="color: var(--udd-blue); font-size: 1.1rem;">{{ $course->course_code }}</span>
                                <span class="badge badge-info">{{ $course->units }} Units</span>
                            </div>
                            <p class="text-main font-bold" style="font-size: 0.95rem; margin-bottom: 0.75rem; line-height: 1.4;">
                                {{ $course->title }}
                            </p>
                            <div class="flex items-center gap-2 text-muted" style="font-size: 0.8rem; font-weight: 700;">
                                <i data-lucide="graduation-cap" style="width: 14px;"></i>
                                Level {{ $course->year_level }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Academic History -->
    @if($completedCourses->count() > 0)
        <div class="card mb-8" style="padding: 0; overflow: hidden;">
            <div style="padding: 1.5rem 2rem; border-bottom: 2px solid var(--border-light);">
                <h2 class="text-main font-extrabold" style="font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="history" class="text-muted" style="width: 20px;"></i>
                    Academic History
                </h2>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: var(--bg-primary); border-bottom: 2px solid var(--border-light);">
                            <th
                                style="padding: 1rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">
                                Course</th>
                            <th
                                style="padding: 1rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">
                                Grading</th>
                            <th
                                style="padding: 1rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">
                                Status</th>
                            <th
                                style="padding: 1rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: right;">
                                Period</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedCourses->sortByDesc('pivot.academic_year') as $course)
                            <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;"
                                onmouseover="this.style.background='var(--border-light)'"
                                onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.25rem 2rem;">
                                    <div class="text-main font-extrabold" style="font-size: 1.05rem;">{{ $course->course_code }}
                                    </div>
                                    <div class="text-muted font-bold" style="font-size: 0.85rem;">{{ $course->title }}</div>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    <span
                                        style="font-size: 1.1rem; font-weight: 800; color: {{ $course->pivot->passed ? 'var(--status-success-text)' : 'var(--status-danger-text)' }};">
                                        {{ number_format((float) $course->pivot->grade, 1) }}
                                    </span>
                                </td>
                                <td style="padding: 1.25rem 1rem;">
                                    @if($course->pivot->passed)
                                        <span class="badge badge-success">
                                            <i data-lucide="check" style="width: 10px;"></i>
                                            Passed
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i data-lucide="x" style="width: 10px;"></i>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td
                                    style="padding: 1.25rem 2rem; text-align: right; color: var(--text-muted); font-size: 0.85rem; font-weight: 700;">
                                    {{ $course->pivot->semester }} {{ $course->pivot->academic_year }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Full Curriculum -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.5rem 2rem; border-bottom: 2px solid var(--border-light); background: var(--bg-primary);">
            <h2 class="text-main font-extrabold" style="font-size: 1.25rem; display: flex; align-items: center; gap: 10px;">
                <i data-lucide="layers" class="text-muted" style="width: 20px;"></i>
                Program Curriculum
            </h2>
            <p class="text-muted font-bold" style="font-size: 0.8rem; margin-top: 4px;">{{ $student->school->name }}</p>
        </div>
        <div style="padding: 2.5rem 2rem;">
            @foreach($allCourses as $yearLevel => $courses)
                <div style="margin-bottom: 3rem;">
                    <div class="flex items-center gap-3 mb-6"
                        style="border-bottom: 2px solid var(--udd-blue-light); padding-bottom: 0.5rem;">
                        <span
                            style="background: var(--udd-blue); color: white; padding: 4px 14px; border-radius: 30px; font-size: 0.75rem; font-weight: 800;">YEAR
                            {{ $yearLevel }}</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
                        @foreach($courses as $course)
                            @php
                                $isCompleted = $completedCourses->where('id', $course->id)->where('pivot.passed', true)->isNotEmpty();
                                $isFailed = $completedCourses->where('id', $course->id)->where('pivot.passed', false)->isNotEmpty();
                                $isEnrolled = $currentCourses->where('id', $course->id)->isNotEmpty();

                                $cardTheme = $isCompleted ? 'success' : ($isFailed ? 'danger' : ($isEnrolled ? 'info' : 'main'));
                            @endphp

                            <div class="card"
                                style="padding: 1.25rem; position: relative; {{ $isEnrolled || $isCompleted ? 'border-width: 2px;' : '' }} {{ $isCompleted ? 'border-color: var(--status-success-text);' : ($isEnrolled ? 'border-color: var(--udd-blue);' : '') }}">
                                @if($isCompleted)
                                    <div
                                        style="position: absolute; top: -10px; right: 10px; background: var(--status-success-text); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm);">
                                        <i data-lucide="check" style="width: 14px;"></i>
                                    </div>
                                @elseif($isEnrolled)
                                    <div
                                        style="position: absolute; top: -10px; right: 10px; background: var(--udd-blue); color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.65rem; font-weight: 800; box-shadow: var(--shadow-sm);">
                                        ACTIVE
                                    </div>
                                @endif

                                <div class="font-extrabold mb-1"
                                    style="color: {{ $isCompleted ? 'var(--status-success-text)' : ($isEnrolled ? 'var(--udd-blue)' : 'var(--text-main)') }}; font-size: 1.1rem;">
                                    {{ $course->course_code }}
                                </div>
                                <div class="text-muted font-bold"
                                    style="font-size: 0.85rem; margin-bottom: 1rem; line-height: 1.3;">
                                    {{ $course->title }}
                                </div>
                                <div class="flex justify-between items-center mt-auto">
                                    <span class="badge badge-{{ $cardTheme }}"
                                        style="padding: 2px 10px; font-size: 0.7rem;">{{ $course->units }} UNITS</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection