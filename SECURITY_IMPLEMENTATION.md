# Security Implementation Guide

## Overview

This document outlines the security measures implemented in the Student Enrollment System to protect against common web vulnerabilities and ensure data integrity.

## Security Features Implemented

### 1. CSRF Protection

**Implementation:**
- Laravel's built-in CSRF protection is enabled by default for all POST, PUT, PATCH, and DELETE requests
- CSRF tokens are automatically included in forms via `@csrf` Blade directive
- Configured in `bootstrap/app.php` via `validateCsrfTokens()` middleware

**Usage:**
```blade
<form method="POST" action="{{ route('enrollment.submit') }}">
    @csrf
    <!-- form fields -->
</form>
```

**AJAX Requests:**
```javascript
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

### 2. Input Sanitization

**Implementation:**
- Custom `SanitizeInput` middleware applied globally to all web routes
- Removes null bytes, trims whitespace, and strips HTML tags from user input
- Located at: `app/Http/Middleware/SanitizeInput.php`

**Features:**
- Automatic sanitization of all request input
- Preserves specific fields that require HTML (comments, descriptions)
- Defense-in-depth approach alongside Laravel's validation

**Configuration:**
```php
// In bootstrap/app.php
$middleware->web(append: [
    \App\Http\Middleware\SanitizeInput::class,
]);
```

### 3. Rate Limiting

**Implementation:**
- Custom `RateLimitEnrollment` middleware for enrollment operations
- Limits: 10 enrollment operations per minute per user
- Located at: `app/Http/Middleware/RateLimitEnrollment.php`

**Protected Routes:**
- Course addition/removal
- Schedule submission
- Enrollment reset operations
- Petition submissions

**Configuration:**
```php
// In routes/web.php
Route::middleware('rate.limit.enrollment')->group(function () {
    Route::post('enrollment/irregular/add-course', ...);
    Route::post('enrollment/submit', ...);
});
```

**Response on Rate Limit:**
- HTTP 429 (Too Many Requests)
- JSON response includes retry_after seconds
- User-friendly error message

### 4. Authentication & Authorization

**Implementation:**
- Separate authentication guards for students and professors
- Custom middleware: `StudentAuth` and `ProfessorAuth`
- Session-based authentication with secure cookies

**Guards:**
```php
// Student guard
Auth::guard('student')->check()

// Professor guard
Auth::guard('professor')->check()
```

**Protected Routes:**
```php
Route::middleware('student.auth')->group(function () {
    // Student-only routes
});

Route::middleware('professor.auth')->group(function () {
    // Professor-only routes
});
```

### 5. Database Security

**Implementation:**
- Parameterized queries via Eloquent ORM (prevents SQL injection)
- Database indexes for performance (see migration: `add_performance_indexes_to_tables.php`)
- Connection pooling configuration in `config/database.php`

**Connection Settings:**
```php
'options' => [
    PDO::ATTR_PERSISTENT => env('DB_PERSISTENT', false),
    PDO::ATTR_TIMEOUT => env('DB_TIMEOUT', 5),
    PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements
]
```

**Indexes Added:**
- Students: school_id, status, composite (school_id, year_level)
- Courses: school_id, is_active, title, composite (school_id, is_active)
- Enrollments: student_id, status, professor_id, composite indexes for queries
- Payments: student_id, status, composite for payment verification
- Enrollment courses: enrollment_id, course_id, schedule-based indexes

### 6. Password Security

**Implementation:**
- Passwords hashed using bcrypt (Laravel default)
- Minimum password requirements enforced via validation
- Password reset functionality with secure tokens

**Configuration:**
```php
// In Student/Professor models
protected $casts = [
    'password' => 'hashed',
];
```

### 7. Session Security

**Implementation:**
- Secure session configuration
- HTTP-only cookies (prevents XSS access to session cookies)
- SameSite cookie attribute for CSRF protection

**Configuration in `.env`:**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true  # Enable in production with HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### 8. Error Handling

**Implementation:**
- Custom exception handlers in `bootstrap/app.php`
- Graceful error messages without exposing sensitive information
- Detailed logging for debugging (not exposed to users)

**Handled Exceptions:**
- Database errors (QueryException)
- Model not found (ModelNotFoundException)
- Authentication errors (AuthenticationException)
- Authorization errors (AuthorizationException)
- Validation errors (ValidationException)
- Network errors (ConnectionException)

### 9. Logging & Monitoring

**Implementation:**
- Comprehensive logging via `EnrollmentLogger` service
- System health monitoring via `MonitorSystemHealth` command
- Performance monitoring via `PerformanceMonitor` service

**Log Channels:**
- Enrollment operations
- Authentication attempts
- Security events
- System errors

### 10. Data Validation

**Implementation:**
- Server-side validation for all user input
- Laravel's validation rules applied in controllers
- Real-time validation feedback for enrollment operations

**Example:**
```php
$request->validate([
    'course_id' => 'required|exists:courses,id',
    'schedule_day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
    'start_time' => 'required|date_format:H:i',
    'end_time' => 'required|date_format:H:i|after:start_time',
]);
```

## Security Best Practices

### For Developers

1. **Always use Eloquent ORM** - Never concatenate SQL queries
2. **Validate all input** - Server-side validation is mandatory
3. **Use CSRF tokens** - Include `@csrf` in all forms
4. **Escape output** - Use Blade's `{{ }}` syntax (auto-escapes)
5. **Use HTTPS in production** - Set `SESSION_SECURE_COOKIE=true`
6. **Keep dependencies updated** - Run `composer update` regularly
7. **Review logs regularly** - Monitor for suspicious activity

### For Deployment

1. **Environment Configuration:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=<generated-key>
   SESSION_SECURE_COOKIE=true
   ```

2. **File Permissions:**
   - Storage directory: 775
   - Bootstrap/cache: 775
   - .env file: 600 (owner read/write only)

3. **Web Server Configuration:**
   - Disable directory listing
   - Configure proper document root (public directory)
   - Enable HTTPS with valid SSL certificate
   - Configure security headers (CSP, X-Frame-Options, etc.)

4. **Database Security:**
   - Use strong database passwords
   - Restrict database user permissions
   - Enable SSL for database connections in production
   - Regular backups with encryption

## Security Checklist

- [x] CSRF protection enabled
- [x] Input sanitization implemented
- [x] Rate limiting on critical operations
- [x] Authentication guards configured
- [x] Authorization middleware applied
- [x] SQL injection prevention (Eloquent ORM)
- [x] XSS prevention (Blade escaping + sanitization)
- [x] Password hashing (bcrypt)
- [x] Secure session configuration
- [x] Error handling without information disclosure
- [x] Comprehensive logging
- [x] Database indexes for performance
- [x] Connection pooling configured
- [ ] HTTPS enabled (production only)
- [ ] Security headers configured (production)
- [ ] Regular security audits scheduled

## Vulnerability Testing

### Recommended Tools

1. **OWASP ZAP** - Web application security scanner
2. **Laravel Security Checker** - Check for known vulnerabilities in dependencies
3. **PHPStan** - Static analysis for PHP code
4. **Psalm** - Static analysis with security focus

### Testing Commands

```bash
# Check for vulnerable dependencies
composer audit

# Run static analysis
./vendor/bin/phpstan analyse

# Run tests
php artisan test
```

## Incident Response

### In Case of Security Breach

1. **Immediate Actions:**
   - Disable affected systems
   - Change all credentials
   - Review logs for breach extent
   - Notify affected users

2. **Investigation:**
   - Analyze logs and audit trails
   - Identify vulnerability exploited
   - Document timeline of events

3. **Remediation:**
   - Patch vulnerability
   - Update security measures
   - Restore from clean backup if needed
   - Monitor for continued attacks

4. **Post-Incident:**
   - Conduct security review
   - Update security policies
   - Provide training if needed
   - Document lessons learned

## Contact

For security concerns or to report vulnerabilities, contact:
- Security Team: security@example.com
- Emergency: +1-XXX-XXX-XXXX

## References

- [Laravel Security Documentation](https://laravel.com/docs/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
