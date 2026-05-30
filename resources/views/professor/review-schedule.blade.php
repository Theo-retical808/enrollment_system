@extends('layouts.professor')

@section('title', 'Review Schedule')

@section('content')
<style>
    :root {
        --text-main: var(--text-primary);
        --text-muted: var(--text-secondary);
        --card-bg: var(--bg-white);
        --card-border: var(--border-light);
        --grad-start: var(--accent-blue);
        --grad-end: var(--accent-blue-text);
        
        --status-success-bg: #f0fdf4;
        --status-success-border: #dcfce7;
        --status-success-text: #166534;

        --status-danger-bg: #fef2f2;
        --status-danger-border: #fee2e2;
        --status-danger-text: #991b1b;

        --status-info-bg: #f0f9ff;
        --status-info-border: #bae6fd;
        --status-info-text: #075985;
        
        --status-warning-bg: #fffbeb;
        --status-warning-border: #fef3c7;
        --status-warning-text: #92400e;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <a href="{{ route('professor.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 8px; text-decoration: none; margin-bottom: 2rem; transition: all 0.2s;" onmouseover="this.style.background='var(--hover-bg)'; this.style.color='var(--text-main)';" onmouseout="this.style.background='var(--card-bg)'; this.style.color='var(--text-muted)';">
        <i data-lucide="arrow-left" style="width: 16px;"></i>
        Back to Dashboard
    </a>
    
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">Schedule Review</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Review and validate student enrollment submission</p>
    </div>

    <!-- Student Profile -->
    <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 1rem;">
            <i data-lucide="user-circle" style="color: var(--status-info-text); width: 24px; height: 24px;"></i>
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">Student Information</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em;">Full Name</span>
                <p style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0.25rem 0 0 0;">{{ $enrollment->student->full_name }}</p>
            </div>
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em;">Student ID</span>
                <p style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0.25rem 0 0 0;">{{ $enrollment->student->student_id }}</p>
            </div>
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em; display: block; margin-bottom: 0.25rem;">Classification</span>
                @if($enrollment->student->isRegular())
                    <span style="background: var(--status-info-bg); color: var(--status-info-text); padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Regular</span>
                @else
                    <span style="background: var(--status-warning-bg); color: var(--status-warning-text); padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">Irregular</span>
                @endif
            </div>
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em;">Affiliation</span>
                <p style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0.25rem 0 0 0;">{{ $enrollment->student->school->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Enrollment Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #eff6ff; color: #3b82f6; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="book-open-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Total Load</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $enrollment->total_units }} <span style="font-size: 1rem; font-weight: 600; color: var(--text-muted);">Units</span></div>
            </div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #ecfdf5; color: #10b981; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="layers" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Course Count</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1;">{{ $enrollment->courses->count() }}</div>
            </div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="background: #fef2f2; color: #ef4444; padding: 0.8rem; border-radius: 12px;">
                <i data-lucide="calendar-check" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Submission Date</div>
                <div style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); line-height: 1; margin-bottom: 4px;">{{ $enrollment->submitted_at->format('M d, Y') }}</div>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">{{ $enrollment->submitted_at->format('h:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Validation Checks -->
    @if(!empty($validationData))
        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 1rem;">
                <i data-lucide="activity" style="color: var(--status-warning-text); width: 24px; height: 24px;"></i>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">Automated Validation Checks</h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach([
                    'prerequisites_valid' => ['label' => 'Prerequisite Compliance', 'error' => 'Prerequisite violations detected'],
                    'unit_load_valid' => ['label' => 'Unit Load Verification', 'error' => 'Unit load limit exceeded'],
                    'no_conflicts' => ['label' => 'Schedule Conflict Analysis', 'error' => 'Time/Room conflicts identified']
                ] as $key => $meta)
                    @if(isset($validationData[$key]))
                        <div style="background: var(--bg-primary); border: 1px solid var(--card-border); padding: 1rem 1.25rem; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: {{ $validationData[$key] ? 'var(--status-success-bg)' : 'var(--status-danger-bg)' }}; color: {{ $validationData[$key] ? 'var(--status-success-text)' : 'var(--status-danger-text)' }};">
                                    <i data-lucide="{{ $validationData[$key] ? 'check-circle' : 'alert-octagon' }}" style="width: 18px;"></i>
                                </div>
                                <span style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $meta['label'] }}</span>
                            </div>
                            <span style="background: {{ $validationData[$key] ? 'var(--status-success-bg)' : 'var(--status-danger-bg)' }}; color: {{ $validationData[$key] ? 'var(--status-success-text)' : 'var(--status-danger-text)' }}; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                                {{ $validationData[$key] ? 'PASSED' : 'FAILED' }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Course List -->
    <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--card-border); display: flex; align-items: center; gap: 12px;">
            <i data-lucide="list-checks" style="color: var(--status-success-text); width: 24px; height: 24px;"></i>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">Detailed Schedule</h3>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--card-bg);">
                    <tr>
                        <th style="padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); text-align: left;">Course</th>
                        <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); text-align: left;">Credits</th>
                        <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); text-align: left;">Day & Time</th>
                        <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); text-align: left;">Location</th>
                        <th style="padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: var(--text-muted); text-align: left;">Instructor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollment->courses as $course)
                        <tr style="border-bottom: 1px solid var(--card-border);">
                            <td style="padding: 1.25rem 1.5rem;">
                                <div style="font-weight: 800; color: var(--text-main); font-size: 0.95rem;">{{ $course->course_code }}</div>
                                <div style="font-weight: 600; color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">{{ $course->title }}</div>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                <span style="background: var(--status-info-bg); color: var(--status-info-text); padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                                    {{ $course->units }} Units
                                </span>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                <div style="font-weight: 800; color: var(--status-info-text); font-size: 0.9rem;">{{ $course->pivot->schedule_day }}</div>
                                <div style="font-weight: 600; color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                                    {{ date('g:i A', strtotime($course->pivot->start_time)) }} - {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: var(--text-main);">
                                    <i data-lucide="map-pin" style="width: 16px; color: var(--text-muted);"></i>
                                    {{ $course->pivot->room ?? 'TBA' }}
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1.5rem;">
                                <div style="font-weight: 700; color: var(--text-muted); font-size: 0.85rem;">
                                    {{ $course->pivot->instructor ?? 'Unassigned' }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Final Decision Form -->
    <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 1rem;">
            <i data-lucide="gavel" style="color: var(--text-main); width: 24px; height: 24px;"></i>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin: 0;">Final Decision</h3>
        </div>
        
        <form id="reviewForm" method="POST" action="{{ route('professor.approve', $enrollment->id) }}">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label for="review_comments" style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">Reviewer Comments (Optional)</label>
                <textarea 
                    id="review_comments" 
                    name="review_comments" 
                    rows="4" 
                    placeholder="Provide feedback to the student regarding their schedule selection..."
                    style="width: 100%; padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid var(--card-border); background: var(--bg-white); color: var(--text-main); font-size: 0.95rem; font-family: inherit; resize: vertical; transition: border-color 0.2s; outline: none;"
                    onfocus="this.style.borderColor='#3b82f6'"
                    onblur="this.style.borderColor='var(--card-border)'"
                ></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="submit" name="action" value="reject" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem 1.5rem; border-radius: 12px; border: none; background: #ef4444; color: white; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                    <i data-lucide="x-circle" style="width: 18px;"></i>
                    Reject Submission
                </button>
                <button type="submit" name="action" value="approve" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem 1.5rem; border-radius: 12px; border: none; background: #10b981; color: white; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                    <i data-lucide="check-circle" style="width: 18px;"></i>
                    Approve Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    const action = e.submitter.value;
    const comments = document.getElementById('review_comments').value.trim();
    
    if (action === 'reject' && comments === '') {
        e.preventDefault();
        alert('Please provide comments when rejecting a schedule.');
        return false;
    }
    
    const confirmMessage = action === 'approve' 
        ? 'Confirm approval of this enrollment schedule?' 
        : 'Confirm rejection of this enrollment schedule?';
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection