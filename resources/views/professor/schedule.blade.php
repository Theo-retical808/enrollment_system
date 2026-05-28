@extends('layouts.professor')

@section('title', 'My Schedule')

@section('content')
<div class="page-header mb-8">
    <h1 class="text-main font-extrabold" style="font-size: 2.2rem; margin-bottom: 0.25rem;">My Schedule</h1>
    <p class="text-muted font-bold">Your assigned class schedule for this semester</p>
</div>

@if($schedules->count() > 0)
<!-- Summary -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--udd-blue);">
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Total Classes</span>
        <h3 class="text-main font-extrabold" style="font-size: 2rem;">{{ $schedules->count() }}</h3>
    </div>
    <div class="card" style="border-left: 4px solid var(--status-success-text);">
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Subjects</span>
        <h3 class="text-main font-extrabold" style="font-size: 2rem;">{{ $schedules->pluck('course_id')->unique()->count() }}</h3>
    </div>
    <div class="card" style="border-left: 4px solid var(--status-info-text);">
        <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Days Active</span>
        <h3 class="text-main font-extrabold" style="font-size: 2rem;">{{ $schedulesByDay->count() }}</h3>
    </div>
</div>

<!-- Schedule by Day -->
@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
    @if(isset($schedulesByDay[$day]))
    <div class="card mb-8" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
        <div style="padding: 1rem 1.5rem; background: var(--bg-primary); border-bottom: 2px solid var(--border-light);">
            <h3 class="text-main font-extrabold" style="font-size: 1.1rem;">{{ $day }}</h3>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <th style="padding: 0.75rem 1.5rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Time</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Subject</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Room</th>
                        <th style="padding: 0.75rem 1rem; font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: left;">Students</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedulesByDay[$day] as $schedule)
                    <tr style="border-bottom: 1px solid var(--border-light);">
                        <td style="padding: 1rem 1.5rem;">
                            <span class="text-main font-extrabold" style="font-size: 0.95rem; color: var(--udd-blue);">{{ $schedule->time_range }}</span>
                        </td>
                        <td style="padding: 1rem;">
                            <div class="text-main font-extrabold">{{ $schedule->course->course_code }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ $schedule->course->title }}</div>
                        </td>
                        <td style="padding: 1rem;">
                            <div class="flex items-center gap-2 text-main font-bold">
                                <i data-lucide="map-pin" style="width: 14px; color: var(--text-muted);"></i>
                                {{ $schedule->room }}
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="text-main font-bold">{{ $schedule->enrolled_count }}</span>
                            <span class="text-muted">/{{ $schedule->max_students }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endforeach

@else
<div class="card" style="text-align: center; padding: 4rem 2rem;">
    <i data-lucide="calendar-x" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem;"></i>
    <h3 class="text-main font-extrabold" style="font-size: 1.25rem; margin-bottom: 0.5rem;">No Schedule Assigned</h3>
    <p class="text-muted">You don't have any classes assigned yet. Please contact the admin.</p>
</div>
@endif
@endsection
