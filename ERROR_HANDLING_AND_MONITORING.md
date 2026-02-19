# Error Handling and Monitoring Implementation

## Overview

This document describes the comprehensive error handling and monitoring system implemented for the Student Enrollment System.

## Error Handling

### Custom Exception Classes

The system includes domain-specific exception classes for better error handling:

1. **EnrollmentException** - Base exception for enrollment-related errors
2. **ValidationException** - Validation errors with detailed feedback
3. **PaymentRequiredException** - Payment verification failures
4. **ScheduleConflictException** - Schedule time conflicts
5. **UnitLoadException** - Unit load limit violations
6. **PrerequisiteException** - Prerequisite requirement failures
7. **DatabaseException** - Database operation errors

### Exception Handling Configuration

Configured in `bootstrap/app.php`:
- Database query errors with graceful degradation
- Model not found errors with user-friendly messages
- Authentication and authorization errors
- Validation errors with enhanced messaging
- Network/timeout errors

### User-Friendly Error Pages

Custom error views for common HTTP errors:
- **404** - Page Not Found
- **403** - Access Denied
- **500** - Server Error
- **503** - Service Unavailable

All error pages include:
- Clear error messages
- Navigation options to return to dashboard
- Debug information (when APP_DEBUG=true)

### Validation Error Components

Reusable Blade components for displaying errors:
- `<x-alert>` - General alert messages (success, error, warning, info)
- `<x-validation-errors>` - Structured validation error display

## Logging System

### Custom Log Channels

Three dedicated log channels configured in `config/logging.php`:

1. **enrollment** - Enrollment workflow events (30-day retention)
2. **performance** - Performance metrics (7-day retention)
3. **audit** - Audit trail (90-day retention)

### EnrollmentLogger Service

Centralized logging service for enrollment events:

```php
// Authentication events
EnrollmentLogger::logAuthentication($student, 'login', true);

// Enrollment actions
EnrollmentLogger::logEnrollmentAction($enrollment, 'submitted');

// Course selection
EnrollmentLogger::logCourseSelection($student, $course, 'added', true);

// Validation events
EnrollmentLogger::logValidation($student, 'prerequisites', false, $details);

// Professor reviews
EnrollmentLogger::logProfessorReview($enrollment, 'approved', 'approved');

// Payment verification
EnrollmentLogger::logPaymentVerification($student, true);

// System errors
EnrollmentLogger::logError('context', $exception, $data);

// Performance metrics
EnrollmentLogger::logPerformance('operation', $duration);
```

## Performance Monitoring

### PerformanceMonitor Service

Tracks system performance and health:

```php
// Start/stop timers
PerformanceMonitor::start('enrollment_submission');
// ... operation ...
$duration = PerformanceMonitor::stop('enrollment_submission');

// Get health metrics
$health = PerformanceMonitor::getHealthMetrics();
// Returns: database, cache, disk_space, memory_usage

// Get enrollment statistics
$stats = PerformanceMonitor::getEnrollmentStats();
// Returns: active_enrollments, pending_reviews, approved_today, total_students
```

### Request Logging Middleware

`LogRequests` middleware tracks all HTTP requests:
- Request ID for tracing
- Method, URL, IP address, user agent
- User identification (student/professor/guest)
- Response status and duration
- Memory usage
- Slow request detection (>2 seconds)

### Health Monitoring Command

Artisan command for system health checks:

```bash
php artisan enrollment:monitor-health
```

Checks:
- Database connectivity
- Cache functionality
- Disk space usage
- Memory usage
- Enrollment statistics
- Alerts for warnings

## Audit Reporting

### AuditReportController Endpoints

Enhanced with monitoring features:

1. **GET /api/audit/enrollment/{id}** - Enrollment audit trail
2. **GET /api/audit/report** - Filtered audit report
3. **GET /api/audit/statistics** - Audit statistics summary
4. **GET /api/audit/export** - Export audit report as CSV
5. **GET /api/audit/health** - System health metrics
6. **GET /api/audit/errors** - Error logs summary

### Audit Log Features

- Complete enrollment lifecycle tracking
- Status transitions with timestamps
- User actions (student/professor/system)
- Comments and metadata
- Searchable and filterable
- CSV export capability

## Usage Examples

### Handling Validation Errors in Controllers

```php
use App\Exceptions\PrerequisiteException;

public function addCourse(Request $request)
{
    $course = Course::find($request->course_id);
    
    if (!$this->hasPrerequisites($student, $course)) {
        throw new PrerequisiteException(
            $course->title,
            $this->getMissingPrerequisites($student, $course)
        );
    }
    
    // Continue with course addition...
}
```

### Performance Tracking

```php
use App\Services\PerformanceMonitor;

public function complexOperation()
{
    PerformanceMonitor::start('complex_operation');
    
    // Perform operation...
    
    PerformanceMonitor::stop('complex_operation', [
        'records_processed' => $count,
        'user_id' => $userId,
    ]);
}
```

### Displaying Errors in Views

```blade
@extends('layouts.app')

@section('content')
    <x-alert type="success" />
    <x-alert type="error" />
    <x-validation-errors />
    
    <!-- Your content -->
@endsection
```

## Monitoring Best Practices

1. **Regular Health Checks** - Schedule the health monitoring command to run periodically
2. **Log Rotation** - Logs are automatically rotated based on retention policies
3. **Performance Thresholds** - Operations >1s are logged as slow operations
4. **Error Tracking** - All exceptions are logged with full context
5. **Audit Trail** - All enrollment actions are logged for compliance

## Configuration

### Environment Variables

```env
LOG_CHANNEL=stack
LOG_LEVEL=debug
LOG_DAILY_DAYS=14
APP_DEBUG=false  # Set to false in production
```

### Scheduled Tasks

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('enrollment:monitor-health')
             ->hourly()
             ->appendOutputTo(storage_path('logs/health-monitor.log'));
}
```

## Troubleshooting

### Viewing Logs

```bash
# Enrollment logs
tail -f storage/logs/enrollment.log

# Performance logs
tail -f storage/logs/performance.log

# Audit logs
tail -f storage/logs/audit.log
```

### Clearing Old Logs

```bash
# Clear logs older than retention period
php artisan log:clear
```

### Testing Error Pages

Visit these URLs to test error pages:
- `/404` - Not Found
- `/403` - Forbidden
- `/500` - Server Error

## Security Considerations

1. **Debug Mode** - Never enable APP_DEBUG in production
2. **Log Sanitization** - PII is not logged in production
3. **Error Messages** - Generic messages shown to users, detailed logs for developers
4. **Access Control** - Audit endpoints should be restricted to administrators
5. **Log Retention** - Logs are retained according to compliance requirements

## Future Enhancements

- Integration with external monitoring services (e.g., Sentry, New Relic)
- Real-time alerting for critical errors
- Dashboard for visualizing metrics
- Automated performance optimization suggestions
- Machine learning for anomaly detection
