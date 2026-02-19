# Schedule Export Implementation

## Overview

This document describes the implementation of the schedule export functionality for the Student Enrollment System. The feature allows students with approved schedules to export their enrollment information in multiple formats.

## Features Implemented

### 1. PDF Export (HTML-based)
- **Route**: `GET /student/schedule/export/pdf`
- **Controller Method**: `StudentDashboardController@exportPdf`
- **View**: `resources/views/student/schedule-pdf.blade.php`
- **Description**: Generates a print-friendly HTML document that can be saved as PDF using the browser's print function
- **Access**: Only available for approved schedules

### 2. CSV Export
- **Route**: `GET /student/schedule/export/csv`
- **Controller Method**: `StudentDashboardController@exportCsv`
- **Description**: Generates a CSV file containing:
  - Student information (ID, name, email, school, year level)
  - Enrollment details (semester, academic year, status, total units)
  - Course schedule (course code, title, units, day, time, room, instructor)
- **Access**: Available for all enrollment statuses

### 3. Email Schedule
- **Route**: `POST /student/schedule/email`
- **Controller Method**: `StudentDashboardController@emailSchedule`
- **Description**: Simulates sending the schedule via email (placeholder for future mail integration)
- **Access**: Only available for approved schedules

## Implementation Details

### Export Buttons Location

1. **Student Dashboard** (`resources/views/student/dashboard.blade.php`)
   - Quick access buttons for PDF and CSV export
   - Only visible when enrollment status is "approved"
   - Located in the enrollment status section

2. **Schedule View** (`resources/views/student/schedule.blade.php`)
   - Full set of export options including:
     - Print Schedule (browser print)
     - Export as PDF
     - Export as CSV
     - Email Schedule
   - Only visible when enrollment status is "approved"

### PDF Export Template

The PDF export template (`schedule-pdf.blade.php`) includes:
- Professional header with university branding
- Student information section
- Enrollment details section
- Approval status banner (for approved schedules)
- Complete course schedule table
- Weekly schedule view
- Footer with generation timestamp and approval information
- Print-optimized CSS styling

### CSV Export Format

The CSV export includes:
```
Student Enrollment Schedule
Student ID,[student_id]
Student Name,[full_name]
School,[school_name]
Semester,[semester]
Academic Year,[academic_year]
Status,[status]
Total Units,[total_units]

Course Code,Course Title,Units,Day,Start Time,End Time,Room,Instructor
[course_data_rows...]
```

## Security Considerations

1. **Authentication Required**: All export routes require student authentication via `student.auth` middleware
2. **Authorization**: PDF and email exports are restricted to approved schedules only
3. **Data Validation**: Enrollment existence is verified before export
4. **No Sensitive Data**: Exports only include schedule-related information

## Usage Instructions

### For Students

1. **To Export as PDF**:
   - Navigate to your schedule page
   - Click "Export as PDF" button
   - Use browser's print function to save as PDF (Ctrl+P or Cmd+P)
   - Select "Save as PDF" as the destination

2. **To Export as CSV**:
   - Navigate to your schedule page
   - Click "Export as CSV" button
   - File will download automatically
   - Open with Excel, Google Sheets, or any spreadsheet application

3. **To Print Schedule**:
   - Navigate to your schedule page
   - Click "Print Schedule" button
   - Browser print dialog will open
   - Select printer or save as PDF

4. **To Email Schedule**:
   - Navigate to your schedule page
   - Click "Email Schedule" button
   - Schedule will be sent to your registered email address

### For Developers

#### Adding PDF Library (Optional Enhancement)

To use a dedicated PDF library like dompdf:

```bash
composer require barryvdh/laravel-dompdf
```

Then update the `exportPdf` method:

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function exportPdf()
{
    $student = Auth::guard('student')->user();
    $currentEnrollment = $student->getCurrentEnrollment();
    
    if (!$currentEnrollment || $currentEnrollment->status !== 'approved') {
        return redirect()->route('student.dashboard')
            ->with('error', 'Only approved schedules can be exported.');
    }
    
    $pdf = Pdf::loadView('student.schedule-pdf', compact('student', 'currentEnrollment'));
    return $pdf->download('schedule-' . $student->student_id . '.pdf');
}
```

#### Implementing Email Functionality

To implement actual email sending:

1. Configure mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@university.edu
MAIL_FROM_NAME="University Enrollment System"
```

2. Create a Mailable class:
```bash
php artisan make:mail ScheduleMail
```

3. Update the `emailSchedule` method:
```php
use App\Mail\ScheduleMail;
use Illuminate\Support\Facades\Mail;

public function emailSchedule()
{
    $student = Auth::guard('student')->user();
    $currentEnrollment = $student->getCurrentEnrollment();
    
    if (!$currentEnrollment || $currentEnrollment->status !== 'approved') {
        return redirect()->route('student.dashboard')
            ->with('error', 'Only approved schedules can be emailed.');
    }
    
    Mail::to($student->email)->send(new ScheduleMail($student, $currentEnrollment));
    
    return redirect()->route('student.schedule')
        ->with('success', 'Schedule has been sent to ' . $student->email);
}
```

## Testing

Test cases are provided in `tests/Feature/ScheduleExportTest.php`:

- `approved_schedule_can_be_exported_as_pdf()` - Verifies PDF export for approved schedules
- `approved_schedule_can_be_exported_as_csv()` - Verifies CSV export functionality
- `non_approved_schedule_cannot_be_exported_as_pdf()` - Ensures only approved schedules can be exported as PDF
- `schedule_can_be_emailed()` - Tests email functionality
- `csv_export_includes_all_course_details()` - Validates CSV content completeness
- `unauthenticated_user_cannot_export_schedule()` - Ensures authentication is required

Run tests with:
```bash
php artisan test --filter=ScheduleExportTest
```

## Requirements Validation

This implementation satisfies **Requirement 13.3**:
- ✅ PDF generation for schedule printing (HTML-based, browser-printable)
- ✅ CSV/Excel export options for student records
- ✅ Email functionality to send schedule to student (placeholder implemented)

## Future Enhancements

1. **PDF Library Integration**: Add dompdf or similar for server-side PDF generation
2. **Email Templates**: Create rich HTML email templates with schedule details
3. **Export History**: Track when students export their schedules
4. **Batch Export**: Allow administrators to export multiple student schedules
5. **Calendar Integration**: Add iCal/Google Calendar export format
6. **QR Code**: Generate QR code for quick schedule access
7. **Mobile App Integration**: API endpoints for mobile app exports

## Troubleshooting

### PDF Export Opens in Browser Instead of Downloading
- This is expected behavior for the HTML-based approach
- Users can use browser's print function (Ctrl+P) to save as PDF
- To force download, implement dompdf library as described above

### CSV File Opens with Incorrect Encoding
- Ensure the CSV is saved with UTF-8 encoding
- Excel users may need to use "Import Data" feature for proper encoding

### Email Not Sending
- Current implementation is a placeholder
- Follow the "Implementing Email Functionality" section to enable actual email sending
- Verify mail configuration in `.env` file

## Related Files

- `app/Http/Controllers/StudentDashboardController.php` - Export controller methods
- `resources/views/student/schedule-pdf.blade.php` - PDF export template
- `resources/views/student/schedule.blade.php` - Schedule view with export buttons
- `resources/views/student/dashboard.blade.php` - Dashboard with quick export buttons
- `routes/web.php` - Export routes definition
- `tests/Feature/ScheduleExportTest.php` - Export functionality tests
