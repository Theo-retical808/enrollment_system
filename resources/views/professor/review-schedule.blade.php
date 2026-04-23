@extends('layouts.professor')

@section('title', 'Review Schedule')

@section('content')
<style>
    /* The "Chameleon" Brain */
    :root {
        /* General Colors */
        --text-main: #0f172a;
        --text-muted: #64748b;
        --text-light: #475569;
        --bg-main: transparent;
        --bg-alt: #f8fafc;
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --border-light: #f1f5f9;

        /* Purple Theme (Professor Accent) */
        --purple-main: #7c3aed;
        --purple-hover: #6d28d9;
        --purple-light: #f3e8ff;
        --purple-border: #d8b4fe;
        --purple-text: #7e22ce;
        --purple-grad-start: #7c3aed;
        --purple-grad-end: #a855f7;

        /* Success/Approve Theme */
        --success-bg: #dcfce7;
        --success-text: #16a34a; 
        --success-text-dark: #166534;
        --success-border: #86efac;
        --success-grad-start: #10b981;
        --success-grad-end: #059669;

        /* Danger/Reject Theme */
        --danger-bg: #fee2e2;
        --danger-text: #dc2626;
        --danger-text-dark: #991b1b;
        --danger-border: #fca5a5;
        --danger-grad-start: #ef4444;
        --danger-grad-end: #dc2626;

        /* Blue/Info Theme */
        --blue-bg: #dbeafe;
        --blue-text: #1e40af;
        --blue-border: #93c5fd;

        /* Form Theme */
        --form-bg-start: #f8fafc;
        --form-bg-end: #f1f5f9;
        
        /* Badges */
        --badge-regular-bg: #eff6ff;
        --badge-regular-text: #2563eb;
        --badge-irregular-bg: #fff7ed;
        --badge-irregular-text: #ea580c;
    }

    /* Dark Mode Colors - Sleek Slate */
    .dark, [data-theme="dark"], [data-bs-theme="dark"] {
        /* General Colors */
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --text-light: #cbd5e1;
        --bg-alt: #0f172a;
        --card-bg: #1e293b;
        --card-border: #334155;
        --border-light: #334155;

        /* Purple Theme */
        --purple-main: #a78bfa;
        --purple-hover: #c4b5fd;
        --purple-light: #4c1d95;
        --purple-border: #5b21b6;
        --purple-text: #d8b4fe;
        --purple-grad-start: #5b21b6;
        --purple-grad-end: #7c3aed;

        /* Success Theme */
        --success-bg: #064e3b;
        --success-text: #22c55e;
        --success-text-dark: #86efac;
        --success-border: #065f46;
        --success-grad-start: #059669;
        --success-grad-end: #047857;

        /* Danger Theme */
        --danger-bg: #7f1d1d;
        --danger-text: #f87171;
        --danger-text-dark: #fca5a5;
        --danger-border: #991b1b;
        --danger-grad-start: #dc2626;
        --danger-grad-end: #b91c1c;

        /* Blue Theme */
        --blue-bg: #1e3a8a;
        --blue-text: #93c5fd;
        --blue-border: #1e40af;

        /* Form Theme */
        --form-bg-start: #1e293b;
        --form-bg-end: #0f172a;
        
        /* Badges */
        --badge-regular-bg: #1e3a8a;
        --badge-regular-text: #60a5fa;
        --badge-irregular-bg: #7c2d12;
        --badge-irregular-text: #fdba74;
    }

    .review-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--purple-main);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        margin-bottom: 24px;
        transition: all 0.2s;
    }

    .back-link:hover {
        color: var(--purple-hover);
        gap: 12px;
    }

    .review-header {
        margin-bottom: 32px;
    }

    .review-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
        letter-spacing: -0.02em;
    }

    .review-subtitle {
        font-size: 16px;
        color: var(--text-muted);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .info-item {
        padding: 16px;
        background: var(--bg-alt);
        border-radius: 8px;
        border: 1px solid var(--card-border);
    }

    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-main);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .summary-card {
        padding: 24px;
        border-radius: 12px;
        text-align: center;
        border: 2px solid;
        transition: all 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .summary-card.blue {
        background: var(--blue-bg);
        border-color: var(--blue-border);
    }

    .summary-card.green {
        background: var(--success-bg);
        border-color: var(--success-border);
    }

    .summary-card.purple {
        background: var(--purple-light);
        border-color: var(--purple-border);
    }

    .summary-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-value {
        font-size: 36px;
        font-weight: 800;
        line-height: 1;
    }

    .summary-card.blue .summary-value { color: var(--blue-text); }
    .summary-card.green .summary-value { color: var(--success-text-dark); }
    .summary-card.purple .summary-value { color: var(--purple-text); }

    .validation-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--card-bg);
        border-radius: 8px;
        border: 1px solid var(--card-border);
        transition: all 0.2s;
    }

    .validation-item:hover {
        border-color: var(--purple-main);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .validation-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .validation-icon.success {
        background: var(--success-bg);
        color: var(--success-text);
    }

    .validation-icon.error {
        background: var(--danger-bg);
        color: var(--danger-text);
    }

    .validation-text {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-main);
    }

    .course-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .course-table thead {
        background: linear-gradient(135deg, var(--purple-grad-start) 0%, var(--purple-grad-end) 100%);
    }

    .course-table th {
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .course-table th:first-child {
        border-top-left-radius: 8px;
    }

    .course-table th:last-child {
        border-top-right-radius: 8px;
    }

    .course-table td {
        padding: 20px 16px;
        font-size: 14px;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-light);
        background: var(--card-bg);
    }

    .course-table tbody tr {
        transition: all 0.2s;
    }

    .course-table tbody tr:hover td {
        background: var(--bg-alt) !important;
    }

    .course-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 8px;
    }

    .course-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 8px;
    }

    .course-code {
        font-weight: 700;
        color: var(--purple-main);
        font-size: 15px;
    }

    .course-title {
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 4px;
    }

    .schedule-time {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .schedule-day {
        font-weight: 600;
        color: var(--purple-main);
        font-size: 13px;
    }

    .schedule-hours {
        color: var(--text-muted);
        font-size: 13px;
    }

    .review-form {
        background: linear-gradient(135deg, var(--form-bg-start) 0%, var(--form-bg-end) 100%);
        border: 2px solid var(--card-border);
        border-radius: 12px;
        padding: 32px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 12px;
    }

    .form-textarea {
        width: 100%;
        padding: 16px;
        background: var(--card-bg);
        color: var(--text-main);
        border: 2px solid var(--card-border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        transition: all 0.2s;
    }

    .form-textarea:focus {
        outline: none;
        border-color: var(--purple-main);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 24px;
    }

    .action-btn {
        padding: 16px 24px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .action-btn.approve {
        background: linear-gradient(135deg, var(--success-grad-start) 0%, var(--success-grad-end) 100%);
        color: white;
    }

    .action-btn.approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.2);
    }

    .action-btn.reject {
        background: linear-gradient(135deg, var(--danger-grad-start) 0%, var(--danger-grad-end) 100%);
        color: white;
    }

    .action-btn.reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .section-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--purple-grad-start) 0%, var(--purple-grad-end) 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
    }

    /* Helper styling for standard cards */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-light);
    }
    
    .card-body {
        padding: 1.5rem;
    }

    @media (max-width: 768px) {
        .action-buttons { grid-template-columns: 1fr; }
        .summary-grid { grid-template-columns: 1fr; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="review-container">
    <a href="{{ route('professor.dashboard') }}" class="back-link">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
        </svg>
        Back to Dashboard
    </a>
    
    <div class="review-header">
        <h1 class="review-title">Schedule Review</h1>
        <p class="review-subtitle">Review and approve student enrollment schedule</p>
    </div>

    <div class="card mb-6" style="margin-bottom: 24px;">
        <div class="card-header">
            <div class="section-header" style="margin-bottom: 0;">
                <div class="section-icon">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="section-title">Student Information</h2>
            </div>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $enrollment->student->full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Student ID</div>
                    <div class="info-value">{{ $enrollment->student->student_id }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">School</div>
                    <div class="info-value">{{ $enrollment->student->school->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Year Level</div>
                    <div class="info-value">Year {{ $enrollment->student->year_level }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Student Type</div>
                    <div class="info-value">
                        @if($enrollment->student->isRegular())
                            <span style="background: var(--badge-regular-bg); color: var(--badge-regular-text); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Regular</span>
                        @else
                            <span style="background: var(--badge-irregular-bg); color: var(--badge-irregular-text); padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;">Irregular</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Submitted</div>
                    <div class="info-value">{{ $enrollment->submitted_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6" style="margin-bottom: 24px;">
        <div class="card-header">
            <div class="section-header" style="margin-bottom: 0;">
                <div class="section-icon">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="section-title">Enrollment Summary</h2>
            </div>
        </div>
        <div class="card-body">
            <div class="summary-grid">
                <div class="summary-card blue">
                    <div class="summary-label">Total Courses</div>
                    <div class="summary-value">{{ $enrollment->courses->count() }}</div>
                </div>
                <div class="summary-card green">
                    <div class="summary-label">Total Units</div>
                    <div class="summary-value">{{ $enrollment->total_units }}</div>
                </div>
                <div class="summary-card purple">
                    <div class="summary-label">Academic Period</div>
                    <div class="summary-value" style="font-size: 20px;">{{ $enrollment->semester }} {{ $enrollment->academic_year }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($validationData))
        <div class="card mb-6" style="margin-bottom: 24px;">
            <div class="card-header">
                <div class="section-header" style="margin-bottom: 0;">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h2 class="section-title">Validation Status</h2>
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; gap: 12px;">
                    @if(isset($validationData['prerequisites_valid']))
                        <div class="validation-item">
                            @if($validationData['prerequisites_valid'])
                                <div class="validation-icon success">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="validation-text">All prerequisites satisfied</span>
                            @else
                                <div class="validation-icon error">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="validation-text">Prerequisite violations detected</span>
                            @endif
                        </div>
                    @endif

                    @if(isset($validationData['unit_load_valid']))
                        <div class="validation-item">
                            @if($validationData['unit_load_valid'])
                                <div class="validation-icon success">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="validation-text">Unit load within limits ({{ $enrollment->total_units }}/21 units)</span>
                            @else
                                <div class="validation-icon error">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="validation-text">Unit load exceeds maximum ({{ $enrollment->total_units }}/21 units)</span>
                            @endif
                        </div>
                    @endif

                    @if(isset($validationData['no_conflicts']))
                        <div class="validation-item">
                            @if($validationData['no_conflicts'])
                                <div class="validation-icon success">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="validation-text">No schedule conflicts detected</span>
                            @else
                                <div class="validation-icon error">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="validation-text">Schedule conflicts detected</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="card mb-6" style="margin-bottom: 24px;">
        <div class="card-header">
            <div class="section-header" style="margin-bottom: 0;">
                <div class="section-icon">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="section-title">Course Schedule</h2>
            </div>
        </div>
        <div class="card-body" style="padding: 0; overflow-x: auto;">
            <table class="course-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                        <th>Units</th>
                        <th>Schedule</th>
                        <th>Room</th>
                        <th>Instructor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollment->courses as $course)
                        <tr>
                            <td>
                                <span class="course-code">{{ $course->course_code }}</span>
                            </td>
                            <td>
                                <div class="course-title">{{ $course->title }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--purple-main);">{{ $course->units }}</span>
                            </td>
                            <td>
                                <div class="schedule-time">
                                    <span class="schedule-day">{{ $course->pivot->schedule_day }}</span>
                                    <span class="schedule-hours">
                                        {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                        {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span style="color: var(--text-muted);">{{ $course->pivot->room ?? 'TBA' }}</span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted);">{{ $course->pivot->instructor ?? 'TBA' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="section-header" style="margin-bottom: 0;">
                <div class="section-icon">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="section-title">Review Decision</h2>
            </div>
        </div>
        <div class="card-body">
            <form id="reviewForm" method="POST" action="{{ route('professor.approve', $enrollment->id) }}" class="review-form">
                @csrf
                
                <div>
                    <label for="review_comments" class="form-label">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline; vertical-align: middle; margin-right: 6px;">
                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                        </svg>
                        Comments (Optional)
                    </label>
                    <textarea 
                        id="review_comments" 
                        name="review_comments" 
                        rows="4" 
                        class="form-textarea"
                        placeholder="Add any comments or feedback for the student..."></textarea>
                </div>

                <div class="action-buttons">
                    <button 
                        type="submit" 
                        name="action" 
                        value="approve"
                        class="action-btn approve">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Approve Schedule
                    </button>
                    <button 
                        type="submit" 
                        name="action" 
                        value="reject"
                        class="action-btn reject">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Reject Schedule
                    </button>
                </div>
            </form>
        </div>
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
        ? 'Are you sure you want to approve this schedule?' 
        : 'Are you sure you want to reject this schedule?';
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});
</script>
@endsection