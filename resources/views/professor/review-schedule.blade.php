@extends('layouts.professor')

@section('title', 'Review Schedule')

@section('content')

<div class="review-container">
    <a href="{{ route('professor.dashboard') }}" class="btn btn-outline mb-6" style="margin-bottom: 2rem; padding: 0.5rem 1rem; font-size: 0.85rem;">
        <i data-lucide="arrow-left" style="width: 16px;"></i>
        Back to Dashboard
    </a>
    
    <div class="page-header mb-8">
        <h1 class="text-main font-extrabold" style="font-size: 2.2rem; margin-bottom: 0.25rem;">Schedule Review</h1>
        <p class="text-muted font-bold">Review and validate student enrollment submission</p>
    </div>

    <!-- Student Profile -->
    <div class="card mb-8">
        <div class="flex items-center gap-3 mb-6" style="padding-bottom: 1rem;">
            <i data-lucide="user-circle" class="text-muted"></i>
            <h2 class="text-main font-extrabold" style="font-size: 1.25rem;">Student Information</h2>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Full Name</span>
                <p class="text-main font-extrabold" style="font-size: 1.1rem;">{{ $enrollment->student->full_name }}</p>
            </div>
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Student ID</span>
                <p class="text-main font-bold">{{ $enrollment->student->student_id }}</p>
            </div>
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Classification</span>
                <div>
                    @php $typeTheme = $enrollment->student->isRegular() ? 'info' : 'warning'; @endphp
                    <span class="badge badge-{{ $typeTheme }}">{{ $enrollment->student->isRegular() ? 'Regular' : 'Irregular' }}</span>
                </div>
            </div>
            <div>
                <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Affiliation</span>
                <p class="text-main font-bold">{{ $enrollment->student->school->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Enrollment Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: var(--bg-card); border-left: 4px solid var(--udd-blue);">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Total Load</span>
                    <h3 class="text-main font-extrabold" style="font-size: 2rem;">{{ $enrollment->total_units }} <span style="font-size: 1rem; opacity: 0.5;">Units</span></h3>
                </div>
                <div style="background: var(--udd-blue-light); color: var(--udd-blue); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="book-open-check"></i>
                </div>
            </div>
        </div>

        <div class="card" style="background: var(--bg-card); border-left: 4px solid var(--status-success-text);">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Course Count</span>
                    <h3 class="text-main font-extrabold" style="font-size: 2rem;">{{ $enrollment->courses->count() }}</h3>
                </div>
                <div style="background: var(--status-success-bg); color: var(--status-success-text); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="layers"></i>
                </div>
            </div>
        </div>

        <div class="card" style="background: var(--bg-card); border-left: 4px solid var(--status-info-text);">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-muted font-bold" style="font-size: 0.75rem; text-transform: uppercase;">Submission Date</span>
                    <p class="text-main font-bold" style="font-size: 1.1rem; margin-top: 0.5rem;">{{ $enrollment->submitted_at->format('M d, Y') }}</p>
                    <p class="text-muted" style="font-size: 0.8rem;">{{ $enrollment->submitted_at->format('h:i A') }}</p>
                </div>
                <div style="background: var(--status-info-bg); color: var(--status-info-text); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="calendar-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Checks -->
    @if(!empty($validationData))
        <div class="card mb-8">
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="activity" class="text-muted"></i>
                <h3 class="text-main font-extrabold" style="font-size: 1.25rem;">Automated Validation Checks</h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach([
                    'prerequisites_valid' => ['label' => 'Prerequisite Compliance', 'error' => 'Prerequisite violations detected'],
                    'unit_load_valid' => ['label' => 'Unit Load Verification', 'error' => 'Unit load limit exceeded'],
                    'no_conflicts' => ['label' => 'Schedule Conflict Analysis', 'error' => 'Time/Room conflicts identified']
                ] as $key => $meta)
                    @if(isset($validationData[$key]))
                        <div style="background: var(--bg-primary); padding: 1rem; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div class="flex items-center gap-3">
                                <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: {{ $validationData[$key] ? 'var(--status-success-bg)' : 'var(--status-danger-bg)' }}; color: {{ $validationData[$key] ? 'var(--status-success-text)' : 'var(--status-danger-text)' }};">
                                    <i data-lucide="{{ $validationData[$key] ? 'check-circle' : 'alert-octagon' }}" style="width: 18px;"></i>
                                </div>
                                <span class="text-main font-bold">{{ $meta['label'] }}</span>
                            </div>
                            <span class="badge badge-{{ $validationData[$key] ? 'success' : 'danger' }}">
                                {{ $validationData[$key] ? 'PASSED' : 'FAILED' }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Course List -->
    <div class="card mb-8" style="padding: 0; overflow: hidden;">
        <div style="padding: 1.5rem 2rem; border-bottom: 2px solid var(--border-light);">
            <div class="flex items-center gap-3">
                <i data-lucide="list-checks" class="text-muted"></i>
                <h3 class="text-main font-extrabold" style="font-size: 1.25rem;">Detailed Schedule</h3>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: var(--bg-primary); border-bottom: 2px solid var(--border-light);">
                        <th style="padding: 1.25rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Course</th>
                        <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Credits</th>
                        <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Day & Time</th>
                        <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Location</th>
                        <th style="padding: 1.25rem 2rem; font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Instructor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollment->courses as $course)
                        <tr style="border-bottom: 1px solid var(--border-light);">
                            <td style="padding: 1.5rem 2rem;">
                                <div class="text-main font-extrabold">{{ $course->course_code }}</div>
                                <div class="text-muted font-bold" style="font-size: 0.85rem;">{{ $course->title }}</div>
                            </td>
                            <td style="padding: 1.5rem 1rem;">
                                <span class="badge badge-info" style="font-size: 0.85rem; padding: 4px 12px;">{{ $course->units }} Units</span>
                            </td>
                            <td style="padding: 1.5rem 1rem;">
                                <div style="line-height: 1.4;">
                                    <div class="text-main font-extrabold" style="font-size: 0.9rem; color: var(--udd-blue);">{{ $course->pivot->schedule_day }}</div>
                                    <div class="text-muted font-bold" style="font-size: 0.85rem;">
                                        {{ date('g:i A', strtotime($course->pivot->start_time)) }} - {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.5rem 1rem;">
                                <div class="flex items-center gap-2 text-main font-bold">
                                    <i data-lucide="map-pin" style="width: 14px; color: var(--text-muted);"></i>
                                    {{ $course->pivot->room ?? 'TBA' }}
                                </div>
                            </td>
                            <td style="padding: 1.5rem 2rem;">
                                <div class="text-muted font-bold" style="font-size: 0.85rem;">{{ $course->pivot->instructor ?? 'Unassigned' }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Final Decision Form -->
    <div class="card" style="background: var(--bg-primary);">
        <div class="flex items-center gap-3 mb-6">
            <i data-lucide="gavel" class="text-muted"></i>
            <h3 class="text-main font-extrabold" style="font-size: 1.25rem;">Final Decision</h3>
        </div>
        
        <form id="reviewForm" method="POST" action="{{ route('professor.approve', $enrollment->id) }}">
            @csrf
            <div class="mb-8">
                <label for="review_comments" class="form-label" style="margin-bottom: 0.75rem;">REVIEWER COMMENTS (OPTIONAL)</label>
                <textarea 
                    id="review_comments" 
                    name="review_comments" 
                    rows="4" 
                    class="form-control"
                    placeholder="Provide feedback to the student regarding their schedule selection..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <button type="submit" name="action" value="approve" class="btn btn-primary" style="background: var(--status-success-text); padding: 1.5rem;">
                    <i data-lucide="check-circle" style="width: 20px;"></i>
                    Approve Schedule
                </button>
                <button type="submit" name="action" value="reject" class="btn" style="background: var(--status-danger-text); color: white; padding: 1.5rem;">
                    <i data-lucide="x-circle" style="width: 20px;"></i>
                    Reject Submission
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