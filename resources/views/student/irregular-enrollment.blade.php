@extends('layouts.app')

@section('nav-links')
    <a href="{{ route('student.dashboard') }}" class="nav-link">Dashboard</a>
    <span style="color: #4f46e5;">{{ $student->full_name }}</span>
    <form method="POST" action="{{ route('student.logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Logout</button>
    </form>
@endsection

@section('content')
<div style="padding: 2rem 0;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #4f46e5; margin-bottom: 0.5rem;">Irregular Student Enrollment</h1>
                <p style="color: #6b7280;">Select your courses manually and build your custom schedule.</p>
            </div>
            <div style="text-align: right;">
                <div style="background: #fef3c7; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #fcd34d; margin-bottom: 0.5rem;">
                    <p style="color: #d97706; font-weight: 600; margin: 0;">Irregular Student</p>
                </div>
                <p style="color: #6b7280; font-size: 0.875rem;">Status: {{ ucfirst($enrollment->status) }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Enrollment Summary -->
        <div class="grid grid-2" style="margin-bottom: 2rem;">
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Enrollment Information</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Student:</strong> {{ $student->full_name }}</p>
                    <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                    <p><strong>School:</strong> {{ $student->school->name }}</p>
                    <p><strong>Year Level:</strong> {{ $student->year_level }}</p>
                    <p><strong>Semester:</strong> {{ $enrollment->semester }}</p>
                    <p><strong>Academic Year:</strong> {{ $enrollment->academic_year }}</p>
                </div>
            </div>
            
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Schedule Validation</h3>
                <div id="validation-summary" style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <div id="unit-load-display">
                        <p><strong>Total Units:</strong> <span id="current-units">{{ $enrollment->total_units }}</span> / 21</p>
                        <div style="background: #e5e7eb; height: 8px; border-radius: 4px; margin: 0.5rem 0;">
                            <div id="unit-progress" style="background: #3b82f6; height: 100%; border-radius: 4px; width: {{ ($enrollment->total_units / 21) * 100 }}%; transition: width 0.3s;"></div>
                        </div>
                        <p style="font-size: 0.875rem; color: #6b7280;"><span id="remaining-units">{{ 21 - $enrollment->total_units }}</span> units remaining</p>
                    </div>
                    
                    <div id="validation-status" style="margin-top: 1rem;">
                        <div id="validation-errors" style="display: none;">
                            <h4 style="color: #dc2626; font-size: 0.875rem; margin-bottom: 0.5rem;">⚠️ Validation Errors:</h4>
                            <ul id="error-list" style="color: #dc2626; font-size: 0.875rem; margin-left: 1rem;"></ul>
                        </div>
                        
                        <div id="validation-warnings" style="display: none;">
                            <h4 style="color: #d97706; font-size: 0.875rem; margin-bottom: 0.5rem;">⚠️ Warnings:</h4>
                            <ul id="warning-list" style="color: #d97706; font-size: 0.875rem; margin-left: 1rem;"></ul>
                        </div>
                        
                        <div id="validation-success" style="display: none;">
                            <p style="color: #059669; font-size: 0.875rem;">✅ Schedule is valid for submission</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div style="border-bottom: 2px solid #e5e7eb; margin-bottom: 2rem;">
            <div style="display: flex; gap: 2rem;">
                <button class="tab-button active" onclick="showTab('courses')" style="padding: 1rem 0; border: none; background: none; color: #4f46e5; border-bottom: 2px solid #4f46e5; font-weight: 600; cursor: pointer;">
                    Course Selection
                </button>
                <button class="tab-button" onclick="showTab('schedule')" style="padding: 1rem 0; border: none; background: none; color: #6b7280; border-bottom: 2px solid transparent; font-weight: 600; cursor: pointer;">
                    My Schedule
                </button>
                <button class="tab-button" onclick="showTab('petitions')" style="padding: 1rem 0; border: none; background: none; color: #6b7280; border-bottom: 2px solid transparent; font-weight: 600; cursor: pointer;">
                    Petitions
                </button>
            </div>
        </div>

        <!-- Course Selection Tab -->
        <div id="courses-tab" class="tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="color: #374151; margin: 0;">Available Courses</h3>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <!-- Debug Test Button -->
                    <button onclick="testCourseSelection()" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                        🔧 Test Course Selection
                    </button>
                    
                    <form method="GET" style="display: flex; gap: 0.5rem;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search courses..." 
                               style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 250px;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Search</button>
                        @if($search)
                            <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            @if($availableCourses->count() > 0)
                <!-- Debug Information -->
                <div style="background: #f0f9ff; border: 1px solid #0ea5e9; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <h4 style="color: #0369a1; margin-bottom: 0.5rem;">📊 Debug Information</h4>
                    <p style="color: #0369a1; font-size: 0.875rem; margin: 0;">
                        <strong>Available Courses:</strong> {{ $availableCourses->count() }} | 
                        <strong>Student Type:</strong> {{ $student->isRegular() ? 'Regular' : 'Irregular' }} |
                        <strong>Failed Courses:</strong> {{ $student->completedCourses()->wherePivot('passed', false)->count() }} |
                        <strong>Current Enrollment:</strong> {{ $enrollment->courses->count() }} courses
                    </p>
                </div>
                
                <div style="overflow-x: auto; background: white; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Course Code</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Course Title</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Units</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Prerequisites</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableCourses as $course)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem; font-weight: 600; color: #4f46e5;">{{ $course->course_code }}</td>
                                <td style="padding: 1rem;">{{ $course->title }}</td>
                                <td style="padding: 1rem; text-align: center;">{{ $course->units }}</td>
                                <td style="padding: 1rem;">
                                    @if($course->prerequisites->count() > 0)
                                        {{ $course->prerequisites->pluck('course_code')->join(', ') }}
                                    @else
                                        <span style="color: #6b7280;">None</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <button onclick="validateAndShowScheduleModal({{ $course->id }}, '{{ $course->course_code }}', '{{ $course->title }}', {{ $course->units }})" 
                                            class="btn btn-primary course-add-btn" 
                                            data-course-id="{{ $course->id }}"
                                            data-schedule-url="{{ route('student.enrollment.irregular.course-schedules', $course->id) }}"
                                            style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                        Add Course
                                    </button>
                                    <div id="course-validation-{{ $course->id }}" class="course-validation-feedback" style="margin-top: 0.5rem; display: none;">
                                        <small class="validation-message"></small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <p style="color: #6b7280; font-size: 1.125rem;">
                        @if($search)
                            No courses found matching "{{ $search }}".
                        @else
                            No available courses found.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- My Schedule Tab -->
        <div id="schedule-tab" class="tab-content" style="display: none;">
            <h3 style="color: #374151; margin-bottom: 1rem;">Your Selected Schedule</h3>
            
            <div id="selected-courses">
                @if($enrollment->courses->count() > 0)
                    <div style="overflow-x: auto; background: white; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #4f46e5; color: white;">
                                    <th style="padding: 1rem; text-align: left;">Course Code</th>
                                    <th style="padding: 1rem; text-align: left;">Course Title</th>
                                    <th style="padding: 1rem; text-align: left;">Units</th>
                                    <th style="padding: 1rem; text-align: left;">Day</th>
                                    <th style="padding: 1rem; text-align: left;">Time</th>
                                    <th style="padding: 1rem; text-align: left;">Room</th>
                                    <th style="padding: 1rem; text-align: left;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollment->courses as $course)
                                <tr style="border-bottom: 1px solid #f3f4f6;" data-course-id="{{ $course->id }}">
                                    <td style="padding: 1rem; font-weight: 600; color: #4f46e5;">{{ $course->course_code }}</td>
                                    <td style="padding: 1rem;">{{ $course->title }}</td>
                                    <td style="padding: 1rem; text-align: center;">{{ $course->units }}</td>
                                    <td style="padding: 1rem;">{{ $course->pivot->schedule_day }}</td>
                                    <td style="padding: 1rem;">
                                        {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                        {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                    </td>
                                    <td style="padding: 1rem;">{{ $course->pivot->room }}</td>
                                    <td style="padding: 1rem;">
                                        <button onclick="removeCourse({{ $course->id }})" 
                                                class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <p style="color: #6b7280; font-size: 1.125rem;">No courses selected yet.</p>
                        <p style="color: #6b7280;">Go to Course Selection tab to add courses.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Petitions Tab -->
        <div id="petitions-tab" class="tab-content" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="color: #374151; margin: 0;">Course Petitions</h3>
                <button onclick="showPetitionModal()" class="btn btn-primary">Submit New Petition</button>
            </div>

            @if($petitions->count() > 0)
                <div style="overflow-x: auto; background: white; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Course</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Status</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Submitted</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Reviewer</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($petitions as $petition)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 1rem;">
                                    <strong>{{ $petition->course->course_code }}</strong><br>
                                    <small style="color: #6b7280;">{{ $petition->course->title }}</small>
                                </td>
                                <td style="padding: 1rem;">
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.875rem; font-weight: 500;
                                        background: {{ $petition->status === 'approved' ? '#f0fdf4' : ($petition->status === 'rejected' ? '#fef2f2' : '#fef3c7') }};
                                        color: {{ $petition->status === 'approved' ? '#16a34a' : ($petition->status === 'rejected' ? '#dc2626' : '#d97706') }};">
                                        {{ ucfirst($petition->status) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem;">{{ $petition->created_at->format('M d, Y') }}</td>
                                <td style="padding: 1rem;">{{ $petition->reviewer->full_name ?? 'Pending' }}</td>
                                <td style="padding: 1rem;">{{ $petition->review_comments ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <p style="color: #6b7280; font-size: 1.125rem;">No petitions submitted yet.</p>
                    <p style="color: #6b7280;">Submit a petition to request opening of failed courses.</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
            @if($enrollment->status === 'draft')
                <form method="POST" action="{{ route('student.enrollment.irregular.submit') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;" 
                            {{ $enrollment->courses->count() === 0 ? 'disabled' : '' }}>
                        Submit for Approval
                    </button>
                </form>
                
                <form method="POST" action="{{ route('student.enrollment.irregular.reset') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="padding: 0.75rem 2rem;" 
                            onclick="return confirm('Are you sure you want to reset your enrollment? This will remove all selected courses.')">
                        Reset Enrollment
                    </button>
                </form>
            @elseif($enrollment->status === 'submitted')
                <div style="text-align: center; padding: 1rem; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 0.5rem;">
                    <p style="color: #d97706; font-weight: 600;">
                        ⏳ Your enrollment is pending approval from {{ $enrollment->professor->full_name ?? 'your assigned professor' }}.
                    </p>
                </div>
            @elseif($enrollment->status === 'approved')
                <div style="text-align: center; padding: 1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem;">
                    <p style="color: #16a34a; font-weight: 600;">
                        ✅ Your enrollment has been approved!
                    </p>
                </div>
            @elseif($enrollment->status === 'rejected')
                <div style="text-align: center; padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <p style="color: #dc2626; font-weight: 600;">
                        ❌ Your enrollment was rejected.
                    </p>
                    @if($enrollment->review_comments)
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">
                            <strong>Comments:</strong> {{ $enrollment->review_comments }}
                        </p>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('student.enrollment.irregular.reset') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        Create New Enrollment
                    </button>
                </form>
            @endif
            
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary" style="padding: 0.75rem 2rem;">
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Schedule Selection Modal -->
<div id="scheduleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 1rem; width: 90%; max-width: 600px;">
        <h3 style="color: #4f46e5; margin-bottom: 1rem;" id="modalTitle">Select Schedule</h3>
        <div id="scheduleOptions">
            <!-- Schedule options will be loaded here -->
        </div>
        <div style="display: flex; gap: 1rem; justify-content: end; margin-top: 2rem;">
            <button onclick="closeScheduleModal()" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>

<!-- Petition Modal -->
<div id="petitionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 1rem; width: 90%; max-width: 500px;">
        <h3 style="color: #4f46e5; margin-bottom: 1rem;">Submit Course Petition</h3>
        <form method="POST" action="{{ route('student.enrollment.irregular.petition') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Failed Course</label>
                <select name="course_id" class="form-input" required>
                    <option value="">Select a course</option>
                    @foreach($failedCourses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Justification</label>
                <textarea name="justification" class="form-input" rows="4" required 
                          placeholder="Explain why you need this course to be opened..."></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: end; margin-top: 2rem;">
                <button type="button" onclick="closePetitionModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Petition</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentCourseId = null;
let validationCache = {};

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.style.color = '#6b7280';
        btn.style.borderBottomColor = 'transparent';
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    // Add active class to selected button
    event.target.style.color = '#4f46e5';
    event.target.style.borderBottomColor = '#4f46e5';
}

// Enhanced course validation before showing modal
function validateAndShowScheduleModal(courseId, courseCode, courseTitle, units) {
    console.log('Validating course:', courseId, courseCode, courseTitle, units);
    
    // Show loading state
    const button = document.querySelector(`button[data-course-id="${courseId}"]`);
    const originalText = button.textContent;
    button.textContent = 'Validating...';
    button.disabled = true;

    // Validate course addition
    fetch('{{ route("student.enrollment.validation.course") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            course_id: courseId
        })
    })
    .then(response => {
        console.log('Validation response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Validation response data:', data);
        button.textContent = originalText;
        button.disabled = false;

        if (data.success && data.can_add) {
            // Course can be added, show schedule modal
            showScheduleModal(courseId, courseCode, courseTitle, units);
            hideValidationFeedback(courseId);
        } else {
            // Course cannot be added, show validation errors
            const reasons = data.reasons || ['Cannot add this course'];
            console.log('Validation failed:', reasons);
            showValidationFeedback(courseId, reasons, 'error');
        }
    })
    .catch(error => {
        console.error('Validation error:', error);
        button.textContent = originalText;
        button.disabled = false;
        showValidationFeedback(courseId, ['Validation failed. Please try again. Check console for details.'], 'error');
    });
}

function showValidationFeedback(courseId, messages, type = 'error') {
    const feedbackDiv = document.getElementById(`course-validation-${courseId}`);
    const messageDiv = feedbackDiv.querySelector('.validation-message');
    
    if (feedbackDiv && messageDiv) {
        messageDiv.innerHTML = messages.join('<br>');
        messageDiv.style.color = type === 'error' ? '#dc2626' : '#d97706';
        feedbackDiv.style.display = 'block';
    }
}

function hideValidationFeedback(courseId) {
    const feedbackDiv = document.getElementById(`course-validation-${courseId}`);
    if (feedbackDiv) {
        feedbackDiv.style.display = 'none';
    }
}

function showScheduleModal(courseId, courseCode, courseTitle, units) {
    console.log('Showing schedule modal for course:', courseId, courseCode);
    currentCourseId = courseId;
    document.getElementById('modalTitle').textContent = `Add ${courseCode} - ${courseTitle} (${units} units)`;
    
    // Show loading in modal
    document.getElementById('scheduleOptions').innerHTML = '<div style="text-align: center; padding: 2rem;">Loading schedule options...</div>';
    document.getElementById('scheduleModal').style.display = 'block';
    
    // Try to get the URL from the button's data attribute first, then fallback to route generation
    const button = document.querySelector(`button[data-course-id="${courseId}"]`);
    let scheduleUrl;
    
    if (button && button.dataset.scheduleUrl) {
        scheduleUrl = button.dataset.scheduleUrl;
    } else {
        // Fallback to route generation
        scheduleUrl = '{{ route("student.enrollment.irregular.course-schedules", ":courseId") }}'.replace(':courseId', courseId);
    }
    
    console.log('Fetching schedule from URL:', scheduleUrl);
    
    // Fetch actual schedule options via AJAX
    fetch(scheduleUrl)
        .then(response => {
            console.log('Schedule fetch response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Schedule fetch response data:', data);
            if (data.success) {
                displayScheduleOptions(data.schedules);
            } else {
                displayScheduleOptions([]);
            }
        })
        .catch(error => {
            console.error('Error fetching schedules:', error);
            document.getElementById('scheduleOptions').innerHTML = `
                <div style="text-align: center; padding: 2rem; color: #dc2626;">
                    <p>Error loading schedule options.</p>
                    <p style="font-size: 0.875rem;">Check console for details.</p>
                </div>
            `;
        });
}

function displayScheduleOptions(schedules) {
    let optionsHtml = '';
    
    if (schedules.length === 0) {
        optionsHtml = `
            <div style="text-align: center; padding: 2rem; color: #6b7280;">
                <p>No schedule options available for this course.</p>
            </div>
        `;
    } else {
        schedules.forEach((option, index) => {
            optionsHtml += `
                <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; cursor: pointer; transition: all 0.2s;" 
                     onclick="selectSchedule('${option.day}', '${option.start_time}', '${option.end_time}', '${option.room}', '${option.instructor}')"
                     onmouseover="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f8fafc'"
                     onmouseout="this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${option.day}</strong><br>
                            <span style="color: #6b7280;">${formatTime(option.start_time)} - ${formatTime(option.end_time)}</span>
                        </div>
                        <div style="text-align: right;">
                            <div>${option.room}</div>
                            <div style="color: #6b7280; font-size: 0.875rem;">${option.instructor}</div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('scheduleOptions').innerHTML = optionsHtml;
}

function selectSchedule(day, startTime, endTime, room, instructor) {
    // Show loading state
    const modal = document.getElementById('scheduleModal');
    const originalContent = modal.innerHTML;
    
    // Add course with selected schedule
    fetch('{{ route("student.enrollment.irregular.add-course") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            course_id: currentCourseId,
            schedule_day: day,
            start_time: startTime,
            end_time: endTime,
            room: room,
            instructor: instructor
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeScheduleModal();
            updateValidationDisplay();
            refreshScheduleTable();
            showSuccessMessage('Course added successfully!');
        } else {
            alert(data.message || 'Failed to add course. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function removeCourse(courseId) {
    if (!confirm('Are you sure you want to remove this course?')) {
        return;
    }
    
    // Show loading state
    const row = document.querySelector(`tr[data-course-id="${courseId}"]`);
    if (row) {
        row.style.opacity = '0.5';
    }
    
    fetch('{{ route("student.enrollment.irregular.remove-course") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            course_id: courseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateValidationDisplay();
            refreshScheduleTable();
            updateCourseAvailability(); // Check if previously conflicted courses are now available
            showSuccessMessage('Course removed successfully!');
        } else {
            if (row) {
                row.style.opacity = '1';
            }
            alert(data.message || 'Failed to remove course. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (row) {
            row.style.opacity = '1';
        }
        alert('An error occurred. Please try again.');
    });
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
    currentCourseId = null;
}

function showPetitionModal() {
    document.getElementById('petitionModal').style.display = 'block';
}

function closePetitionModal() {
    document.getElementById('petitionModal').style.display = 'none';
}

function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

// Real-time validation functions
function updateValidationDisplay() {
    fetch('{{ route("student.enrollment.validation.feedback") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const summary = data.summary;
                const validation = data.validation;
                const detailed = data.detailed_feedback;
                
                // Update unit load display
                document.getElementById('current-units').textContent = summary.total_units;
                document.getElementById('remaining-units').textContent = summary.remaining_units;
                
                // Update progress bar
                const progressPercent = Math.min(summary.unit_percentage, 100);
                document.getElementById('unit-progress').style.width = progressPercent + '%';
                
                // Update progress bar color based on load
                const progressBar = document.getElementById('unit-progress');
                if (summary.total_units > 21) {
                    progressBar.style.background = '#dc2626'; // Red for over limit
                } else if (summary.total_units > 18) {
                    progressBar.style.background = '#d97706'; // Orange for high load
                } else {
                    progressBar.style.background = '#3b82f6'; // Blue for normal
                }
                
                // Update validation status
                const errorsDiv = document.getElementById('validation-errors');
                const warningsDiv = document.getElementById('validation-warnings');
                const successDiv = document.getElementById('validation-success');
                
                // Clear previous status
                errorsDiv.style.display = 'none';
                warningsDiv.style.display = 'none';
                successDiv.style.display = 'none';
                
                // Show errors if any
                if (validation.errors && validation.errors.length > 0) {
                    const errorList = document.getElementById('error-list');
                    errorList.innerHTML = '';
                    validation.errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                    errorsDiv.style.display = 'block';
                }
                
                // Show warnings if any
                if (validation.warnings && validation.warnings.length > 0) {
                    const warningList = document.getElementById('warning-list');
                    warningList.innerHTML = '';
                    validation.warnings.forEach(warning => {
                        const li = document.createElement('li');
                        li.textContent = warning;
                        warningList.appendChild(li);
                    });
                    warningsDiv.style.display = 'block';
                }
                
                // Show success if valid
                if (validation.is_valid) {
                    successDiv.style.display = 'block';
                }
                
                // Update submit button state
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = !validation.is_valid || summary.total_courses === 0;
                    if (!validation.is_valid || summary.total_courses === 0) {
                        submitButton.style.opacity = '0.5';
                        submitButton.style.cursor = 'not-allowed';
                        submitButton.title = summary.total_courses === 0 
                            ? 'Please add at least one course before submitting'
                            : 'Please resolve validation errors before submitting';
                    } else {
                        submitButton.style.opacity = '1';
                        submitButton.style.cursor = 'pointer';
                        submitButton.title = '';
                    }
                }
                
                // Update course availability based on detailed feedback
                updateCourseButtonStates(detailed);
            }
        })
        .catch(error => {
            console.error('Validation update failed:', error);
        });
}

// Update course button states based on detailed validation feedback
function updateCourseButtonStates(detailedFeedback) {
    const courseButtons = document.querySelectorAll('.course-add-btn');
    
    courseButtons.forEach(button => {
        const courseId = parseInt(button.getAttribute('data-course-id'));
        
        // Check if this course has prerequisite violations
        const hasPrereqViolation = detailedFeedback.prerequisite_violations.some(
            violation => violation.course_id === courseId
        );
        
        if (hasPrereqViolation) {
            button.style.opacity = '0.6';
            button.title = 'Prerequisites not met';
            showValidationFeedback(courseId, ['Prerequisites not satisfied'], 'warning');
        } else {
            button.style.opacity = '1';
            button.title = '';
            hideValidationFeedback(courseId);
        }
    });
}

// Update course availability after removing courses (conflict resolution)
function updateCourseAvailability() {
    const courseButtons = document.querySelectorAll('.course-add-btn');
    courseButtons.forEach(button => {
        const courseId = button.getAttribute('data-course-id');
        hideValidationFeedback(courseId);
        
        // Re-enable button if it was disabled
        if (button.disabled) {
            button.disabled = false;
            button.style.opacity = '1';
            button.style.cursor = 'pointer';
        }
    });
}

// Enhanced conflict resolution - show which courses become available
function showConflictResolution(courseId) {
    fetch('{{ route("student.enrollment.validation.conflict-resolution") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            course_id: courseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.resolved_courses.length > 0) {
            const courseRow = document.querySelector(`tr[data-course-id="${courseId}"]`);
            if (courseRow) {
                // Add conflict resolution info to the row
                let existingInfo = courseRow.querySelector('.conflict-resolution-info');
                if (!existingInfo) {
                    const infoDiv = document.createElement('div');
                    infoDiv.className = 'conflict-resolution-info';
                    infoDiv.style.cssText = 'margin-top: 0.5rem; padding: 0.5rem; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 0.375rem; font-size: 0.875rem;';
                    
                    const resolvedList = data.resolved_courses.map(course => 
                        `${course.course_code} (${course.units} units)`
                    ).join(', ');
                    
                    infoDiv.innerHTML = `
                        <strong style="color: #d97706;">💡 Removing this course would make available:</strong><br>
                        <span style="color: #92400e;">${resolvedList}</span>
                    `;
                    
                    const actionCell = courseRow.querySelector('td:last-child');
                    actionCell.appendChild(infoDiv);
                }
            }
        }
    })
    .catch(error => {
        console.error('Conflict resolution check failed:', error);
    });
}

// Enhanced remove course function with conflict resolution
function removeCourse(courseId) {
    // First show what courses would become available
    showConflictResolution(courseId);
    
    // Add a small delay to show the conflict resolution info
    setTimeout(() => {
        if (!confirm('Are you sure you want to remove this course? Check the highlighted information above for courses that would become available.')) {
            // Remove the conflict resolution info if user cancels
            const infoDiv = document.querySelector(`tr[data-course-id="${courseId}"] .conflict-resolution-info`);
            if (infoDiv) {
                infoDiv.remove();
            }
            return;
        }
        
        // Proceed with removal
        performCourseRemoval(courseId);
    }, 1000);
}

function performCourseRemoval(courseId) {
    // Show loading state
    const row = document.querySelector(`tr[data-course-id="${courseId}"]`);
    if (row) {
        row.style.opacity = '0.5';
    }
    
    fetch('{{ route("student.enrollment.irregular.remove-course") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            course_id: courseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateValidationDisplay();
            refreshScheduleTable();
            updateCourseAvailability(); // Check if previously conflicted courses are now available
            showSuccessMessage('Course removed successfully! Check available courses for newly resolved conflicts.');
        } else {
            if (row) {
                row.style.opacity = '1';
            }
            alert(data.message || 'Failed to remove course. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (row) {
            row.style.opacity = '1';
        }
        alert('An error occurred. Please try again.');
    });
}

// Refresh the schedule table without full page reload
function refreshScheduleTable() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newScheduleContent = doc.querySelector('#selected-courses');
        
        if (newScheduleContent) {
            document.getElementById('selected-courses').innerHTML = newScheduleContent.innerHTML;
        }
    })
    .catch(error => {
        console.error('Failed to refresh schedule table:', error);
        // Fallback to page reload
        location.reload();
    });
}

// Show success messages
function showSuccessMessage(message) {
    // Create temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success';
    successDiv.textContent = message;
    successDiv.style.position = 'fixed';
    successDiv.style.top = '20px';
    successDiv.style.right = '20px';
    successDiv.style.zIndex = '9999';
    successDiv.style.padding = '1rem';
    successDiv.style.backgroundColor = '#f0fdf4';
    successDiv.style.border = '1px solid #bbf7d0';
    successDiv.style.borderRadius = '0.5rem';
    successDiv.style.color = '#16a34a';
    
    document.body.appendChild(successDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        if (successDiv.parentNode) {
            successDiv.parentNode.removeChild(successDiv);
        }
    }, 3000);
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const scheduleModal = document.getElementById('scheduleModal');
    const petitionModal = document.getElementById('petitionModal');
    
    if (event.target === scheduleModal) {
        closeScheduleModal();
    }
    if (event.target === petitionModal) {
        closePetitionModal();
    }
});

// Update validation display when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateValidationDisplay();
    
    // Set up periodic validation updates (every 30 seconds)
    setInterval(updateValidationDisplay, 30000);
});

// Test function for debugging course selection
function testCourseSelection() {
    console.log('Testing course selection functionality...');
    
    // Test 1: Check if we can fetch validation feedback
    fetch('{{ route("student.enrollment.validation.feedback") }}')
        .then(response => response.json())
        .then(data => {
            console.log('✅ Validation feedback test:', data);
            
            // Test 2: Try to validate adding the first available course
            const firstCourseButton = document.querySelector('.course-add-btn');
            if (firstCourseButton) {
                const courseId = firstCourseButton.getAttribute('data-course-id');
                console.log('Testing course validation for course ID:', courseId);
                
                fetch('{{ route("student.enrollment.validation.course") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        course_id: courseId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Course validation test:', data);
                    
                    // Test 3: Try to fetch schedule options
                    const testScheduleUrl = '{{ route("student.enrollment.irregular.course-schedules", ":courseId") }}'.replace(':courseId', courseId);
                    fetch(testScheduleUrl)
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ Schedule options test:', data);
                            alert('Course selection test completed! Check console for results.');
                        })
                        .catch(error => {
                            console.error('❌ Schedule options test failed:', error);
                            alert('Schedule options test failed. Check console for details.');
                        });
                })
                .catch(error => {
                    console.error('❌ Course validation test failed:', error);
                    alert('Course validation test failed. Check console for details.');
                });
            } else {
                console.log('❌ No course buttons found');
                alert('No course buttons found. This might be the issue!');
            }
        })
        .catch(error => {
            console.error('❌ Validation feedback test failed:', error);
            alert('Validation feedback test failed. Check console for details.');
        });
}

// Debounced validation for better performance
let validationTimeout;
function debouncedValidationUpdate() {
    clearTimeout(validationTimeout);
    validationTimeout = setTimeout(updateValidationDisplay, 500);
}
</script>
@endsectionimit
                } else if (summary.total_units > 18) {
                    progressBar.style.background = '#d97706'; // Orange for high load
                } else {
                    progressBar.style.background = '#3b82f6'; // Blue for normal
                }
                
                // Update validation status
                const errorsDiv = document.getElementById('validation-errors');
                const warningsDiv = document.getElementById('validation-warnings');
                const successDiv = document.getElementById('validation-success');
                
                // Clear previous status
                errorsDiv.style.display = 'none';
                warningsDiv.style.display = 'none';
                successDiv.style.display = 'none';
                
                // Show errors if any
                if (validation.errors && validation.errors.length > 0) {
                    const errorList = document.getElementById('error-list');
                    errorList.innerHTML = '';
                    validation.errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                    errorsDiv.style.display = 'block';
                }
                
                // Show warnings if any
                if (validation.warnings && validation.warnings.length > 0) {
                    const warningList = document.getElementById('warning-list');
                    warningList.innerHTML = '';
                    validation.warnings.forEach(warning => {
                        const li = document.createElement('li');
                        li.textContent = warning;
                        warningList.appendChild(li);
                    });
                    warningsDiv.style.display = 'block';
                }
                
                // Show success if valid
                if (validation.is_valid) {
                    successDiv.style.display = 'block';
                }
                
                // Update submit button state
                const submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = !validation.is_valid;
                    if (!validation.is_valid) {
                        submitButton.style.opacity = '0.5';
                        submitButton.style.cursor = 'not-allowed';
                    } else {
                        submitButton.style.opacity = '1';
                        submitButton.style.cursor = 'pointer';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Validation update failed:', error);
        });
}

// Update validation display when page loads and after course changes
document.addEventListener('DOMContentLoaded', function() {
    updateValidationDisplay();
});

// Override the existing selectSchedule and removeCourse functions to include validation updates
const originalSelectSchedule = selectSchedule;
selectSchedule = function(day, startTime, endTime, room, instructor) {
    originalSelectSchedule(day, startTime, endTime, room, instructor);
    setTimeout(updateValidationDisplay, 500); // Update after course is added
};

const originalRemoveCourse = removeCourse;
removeCourse = function(courseId) {
    originalRemoveCourse(courseId);
    setTimeout(updateValidationDisplay, 500); // Update after course is removed
};
</script>
@endsection