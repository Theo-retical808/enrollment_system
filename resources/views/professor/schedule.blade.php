@extends('layouts.professor')

@section('title', 'My Schedule')

@section('content')
<style>
    :root {
        --text-main: var(--text-primary);
        --text-muted: var(--text-secondary);
        --card-bg: var(--bg-white);
        --card-border: var(--border-light);
        --grad-start: var(--accent-blue);
        --grad-end: var(--accent-blue-text);
        
        --blue-bg: var(--accent-blue);
        --blue-text: var(--accent-blue-text);

        --status-success-bg: #f0fdf4;
        --status-success-border: #dcfce7;
        --status-success-text: #166534;

        --status-info-bg: #f0f9ff;
        --status-info-border: #bae6fd;
        --status-info-text: #075985;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">My Schedule</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Your assigned class schedule for this semester</p>
    </div>

    @if($schedules->count() > 0)
    <!-- Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #eff6ff; color: #3b82f6; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="calendar" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Total Classes</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $schedules->count() }}</div>
            </div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #ecfdf5; color: #10b981; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="book-open" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Subjects</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $schedules->pluck('course_id')->unique()->count() }}</div>
            </div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #fdf4ff; color: #d946ef; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Days Active</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $schedulesByDay->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Weekly Timeline -->
    <div style="margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-muted); display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                <i data-lucide="calendar-days" style="width: 20px; height: 20px;"></i>
                Weekly Timeline
            </h2>
        </div>
        
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 24px; padding: 2rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);">
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1rem;">
                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                    <div style="border-right: {{ !$loop->last ? '1px solid var(--border-light)' : 'none' }}; padding-right: {{ !$loop->last ? '1rem' : '0' }};">
                        <h4 style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1.5rem; text-align: center;">
                            {{ substr($day, 0, 3) }}
                        </h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @if(isset($schedulesByDay[$day]) && count($schedulesByDay[$day]) > 0)
                                @php
                                    // Sort by time
                                    $daySchedules = $schedulesByDay[$day]->toArray();
                                    usort($daySchedules, function($a, $b) {
                                        return strtotime(explode(' - ', $a['time_range'])[0]) - strtotime(explode(' - ', $b['time_range'])[0]);
                                    });
                                @endphp
                                @foreach($daySchedules as $scheduleArray)
                                    @php
                                        // Retrieve the original model to access relations properly
                                        $schedule = $schedulesByDay[$day]->where('id', $scheduleArray['id'])->first();
                                        $prefix = substr($schedule->course->course_code, 0, 2);
                                        $theme = match($prefix) {
                                            'CS' => ['primary' => '#2563eb', 'bg' => '#eff6ff'],
                                            'MATH' => ['primary' => '#db2777', 'bg' => '#fdf2f8'],
                                            'PHYS' => ['primary' => '#7c3aed', 'bg' => '#f5f3ff'],
                                            'ENGL' => ['primary' => '#ea580c', 'bg' => '#fff7ed'],
                                            default => ['primary' => '#4b5563', 'bg' => '#f8fafc'],
                                        };
                                    @endphp
                                    <div style="background: {{ $theme['bg'] }}; border-radius: 12px; padding: 0.85rem; border: 1px solid rgba(0,0,0,0.02); transition: all 0.2s ease; cursor: default;" onmouseover="this.style.transform='scale(1.02)';" onmouseout="this.style.transform='scale(1)';">
                                        <div style="font-weight: 800; color: {{ $theme['primary'] }}; font-size: 0.8rem; margin-bottom: 4px;">
                                            {{ $schedule->course->course_code }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: var(--text-main); font-weight: 700; margin-bottom: 4px;">
                                            {{ explode(' - ', $schedule->time_range)[0] }}
                                        </div>
                                        <div style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 3px;">
                                                <i data-lucide="map-pin" style="width: 10px; height: 10px;"></i>
                                                {{ $schedule->room ?? 'TBA' }}
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 3px;">
                                                <i data-lucide="users" style="width: 10px; height: 10px;"></i>
                                                {{ $schedule->enrolled_count }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div style="height: 60px; border-radius: 12px; background: var(--border-light); display: flex; align-items: center; justify-content: center; opacity: 0.5;">
                                    <div style="width: 4px; height: 4px; background: var(--text-muted); border-radius: 50%;"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detailed List -->
    <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.05em;">
        <i data-lucide="list-checks" style="width: 18px; height: 18px;"></i>
        Detailed Schedule List
    </h2>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
            @if(isset($schedulesByDay[$day]) && count($schedulesByDay[$day]) > 0)
                <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden; margin-bottom: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <div style="padding: 1rem 1.5rem; background: var(--bg-primary); border-bottom: 1px solid var(--card-border);">
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;">{{ $day }}</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: var(--card-bg);">
                                <tr style="border-bottom: 1px solid var(--card-border);">
                                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-align: left; width: 180px;">Time</th>
                                    <th style="padding: 1rem; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-align: left;">Subject</th>
                                    <th style="padding: 1rem; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-align: left;">Room</th>
                                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-align: right;">Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedulesByDay[$day] as $schedule)
                                    @php
                                        $prefix = substr($schedule->course->course_code, 0, 2);
                                        $theme = match($prefix) {
                                            'CS' => ['icon' => 'code-2', 'color' => '#2563eb', 'bg' => '#eff6ff'],
                                            'MATH' => ['icon' => 'variable', 'color' => '#db2777', 'bg' => '#fdf2f8'],
                                            'PHYS' => ['icon' => 'atom', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                                            'ENGL' => ['icon' => 'languages', 'color' => '#ea580c', 'bg' => '#fff7ed'],
                                            default => ['icon' => 'book', 'color' => '#4b5563', 'bg' => '#f8fafc'],
                                        };
                                    @endphp
                                    <tr style="border-bottom: 1px solid var(--card-border);">
                                        <td style="padding: 1.25rem 1.5rem;">
                                            <span style="font-weight: 800; color: var(--status-info-text); font-size: 0.95rem;">{{ $schedule->time_range }}</span>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div style="width: 36px; height: 36px; background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i data-lucide="{{ $theme['icon'] }}" style="width: 18px; height: 18px;"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 800; color: var(--text-main); font-size: 0.95rem;">{{ $schedule->course->course_code }}</div>
                                                    <div style="font-weight: 600; color: var(--text-muted); font-size: 0.85rem; margin-top: 2px;">{{ $schedule->course->title }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; color: var(--text-main);">
                                                <i data-lucide="map-pin" style="width: 16px; color: var(--text-muted);"></i>
                                                {{ $schedule->room }}
                                            </div>
                                        </td>
                                        <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                            <span style="font-weight: 800; color: var(--text-main); font-size: 1rem;">{{ $schedule->enrolled_count }}</span>
                                            <span style="font-weight: 600; color: var(--text-muted); font-size: 0.85rem;">/{{ $schedule->max_students }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    
    @else
    <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 4rem 2rem; text-align: center;">
        <i data-lucide="calendar-x" style="width: 48px; height: 48px; color: var(--text-muted); margin: 0 auto 1rem; display: block;"></i>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">No Schedule Assigned</h3>
        <p style="color: var(--text-muted);">You don't have any classes assigned yet. Please contact the admin.</p>
    </div>
    @endif
</div>
@endsection
