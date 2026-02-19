<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Schedule - {{ $student->student_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #4f46e5;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #6b7280;
            font-size: 14px;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        
        .info-box h3 {
            color: #374151;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 5px;
        }
        
        .info-box p {
            margin: 5px 0;
            font-size: 12px;
        }
        
        .info-box strong {
            color: #374151;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .schedule-table thead {
            background: #4f46e5;
            color: white;
        }
        
        .schedule-table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
        }
        
        .schedule-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .schedule-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .summary {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .summary p {
            color: #16a34a;
            font-weight: 600;
            margin: 5px 0;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Official Student Schedule</h1>
        <p>{{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Student Information</h3>
            <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
            <p><strong>Name:</strong> {{ $student->full_name }}</p>
            <p><strong>Email:</strong> {{ $student->email }}</p>
            <p><strong>School:</strong> {{ $student->school->name ?? 'N/A' }}</p>
            <p><strong>Year Level:</strong> {{ $student->year_level }}</p>
        </div>
        
        <div class="info-box">
            <h3>Enrollment Details</h3>
            <p><strong>Status:</strong> {{ ucfirst($currentEnrollment->status) }}</p>
            <p><strong>Semester:</strong> {{ $currentEnrollment->semester }}</p>
            <p><strong>Academic Year:</strong> {{ $currentEnrollment->academic_year }}</p>
            <p><strong>Total Courses:</strong> {{ $currentEnrollment->courses->count() }}</p>
            <p><strong>Total Units:</strong> {{ $currentEnrollment->courses->sum('units') }}</p>
            @if($currentEnrollment->professor)
                <p><strong>Reviewed by:</strong> {{ $currentEnrollment->professor->full_name }}</p>
            @endif
        </div>
    </div>

    @if($currentEnrollment->status === 'approved')
    <div class="summary">
        <p>✓ This schedule has been officially approved and is your authoritative enrollment record for {{ $currentEnrollment->semester }} {{ $currentEnrollment->academic_year }}.</p>
        @if($currentEnrollment->review_comments)
            <p style="margin-top: 10px;"><strong>Professor's Comments:</strong> {{ $currentEnrollment->review_comments }}</p>
        @endif
    </div>
    @endif

    <h3 style="color: #374151; margin-bottom: 15px; font-size: 16px;">Course Schedule</h3>
    
    <table class="schedule-table">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th style="text-align: center;">Units</th>
                <th>Day</th>
                <th>Time</th>
                <th>Room</th>
                <th>Instructor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($currentEnrollment->courses as $course)
            <tr>
                <td style="font-weight: 600; color: #4f46e5;">{{ $course->course_code }}</td>
                <td>{{ $course->title }}</td>
                <td style="text-align: center; font-weight: 600;">{{ $course->units }}</td>
                <td>{{ $course->pivot->schedule_day ?? 'TBA' }}</td>
                <td>
                    @if($course->pivot->start_time && $course->pivot->end_time)
                        {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                        {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                    @else
                        TBA
                    @endif
                </td>
                <td>{{ $course->pivot->room ?? 'TBA' }}</td>
                <td>{{ $course->pivot->instructor ?? 'TBA' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ date('F d, Y g:i A') }}</p>
        <p>This is an official document from the Student Enrollment System</p>
        @if($currentEnrollment->status === 'approved')
            <p style="margin-top: 10px; font-weight: 600;">Approved on {{ $currentEnrollment->reviewed_at->format('F d, Y g:i A') }}</p>
        @endif
    </div>
</body>
</html>
