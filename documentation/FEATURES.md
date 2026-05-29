# System Features

## Overview

The University Enrollment Management System provides a complete academic workflow covering student enrollment, payment processing, professor grading, schedule management, and administrative oversight. The system supports three user roles: **Students**, **Professors**, and **Admins**.

---

## 1. Enrollment System

### Regular Student Enrollment
- Automatic schedule assignment based on the student's program and year level
- Pre-built curriculum templates define the default course load
- Students review their assigned schedule and submit for professor approval
- One-click enrollment submission

### Irregular Student Enrollment
- Manual course selection for students with failed or incomplete courses
- Real-time validation feedback via AJAX:
  - Prerequisite checking (cached for performance)
  - Schedule conflict detection (time overlap on same day)
  - Unit load validation (maximum 21 units, recommended 12–18)
- Add/remove courses dynamically
- Course schedule selection from available time slots
- Conflict resolution suggestions (shows which courses become available if one is removed)

### Enrollment Workflow
```
Draft → Submitted → Approved / Rejected
                         ↓
                    Finalized (locked)
```
- **Draft:** Student is building their schedule
- **Submitted:** Awaiting professor review
- **Approved:** Schedule is finalized and locked
- **Rejected:** Student can modify and resubmit

### Petition System
- Students can petition to retake failed courses
- Requires written justification (10–1000 characters)
- Prevents duplicate pending petitions for the same course
- Professor review and decision tracking

---

## 2. Payment System

### Payment Types
- Enrollment fee
- Tuition
- Miscellaneous fees

### Payment Workflow
- Payment verification required before accessing enrollment features
- Automatic semester and academic year detection
- Payment statuses: `pending`, `paid`, `failed`, `rejected`

### Admin Payment Management
- Manually record payments for students
- Confirm or reject pending payments
- View payment history with search and filtering
- Revenue tracking and statistics

---

## 3. Grading System

### Professor Grading Interface
- View all courses assigned to the professor
- See enrolled students grouped by course
- Submit numeric grades (1.0–5.0 scale)
- Automatic letter grade conversion
- Pass/fail determination (1.0–3.0 = pass, 3.1–5.0 = fail)

### Academic Record Updates
- Grades automatically update `student_completed_courses`
- Student classification cache is cleared on grade submission
- Grade history tracked with semester and academic year

---

## 4. Schedule Management

### Course Schedules
- Day, time, room, and instructor assignment
- Active/inactive schedule status
- Professor-specific schedule views

### Schedule Export
- **PDF Export:** Browser-printable HTML format
- **CSV Export:** Downloadable spreadsheet with full course details
- **Email:** Send approved schedule to student's email

### Curriculum Templates
- Define default course loads by program, year level, and semester
- Used for automatic regular student enrollment
- Admin-managed through the dashboard

---

## 5. Student Dashboard

| Feature | Description |
|---------|-------------|
| Enrollment Status | Current enrollment state and progress |
| Classification | Regular or irregular student indicator |
| Payment Status | Fee verification status |
| Recommended Action | System-suggested next step |
| Course History | Completed courses with grades |
| Schedule View | Current semester schedule |
| Export Options | PDF, CSV, and email schedule |
| Unit Statistics | Completed units, failed units, course counts |

---

## 6. Professor Dashboard

| Feature | Description |
|---------|-------------|
| Review Queue | Pending enrollment submissions from students in their school |
| Recent Reviews | History of approved/rejected enrollments |
| Teaching Schedule | Assigned courses with day/time/room |
| Grading | Submit and manage student grades |
| Audit Access | View enrollment audit trails and reports |

### Enrollment Review
- View student's proposed schedule with full course details
- Access student's completed course history
- Approve or reject with comments
- Approved enrollments are finalized and locked
- Rejected enrollments allow student resubmission

---

## 7. Admin Dashboard

### System Overview
- Total students, professors, enrollments
- Pending vs. approved enrollment counts
- Payment statistics and total revenue
- Recent activity feeds

### Account Management
- **Students:** Create, edit, delete student accounts
- **Professors:** Create, edit, delete professor accounts
- **Search:** Filter by ID, name, or email across all account types

### Enrollment Management
- View all enrollments with status filtering
- Override enrollment decisions (approve/reject regardless of professor)
- Admin comments tracked separately

### Course & Schedule Management
- Create, edit, delete courses
- Assign professors to courses
- Manage course schedules (day, time, room)
- Toggle enrollment assistant status for professors

### Curriculum Management
- Create and manage curriculum templates
- Define default schedules by program/year/semester

---

## 8. Audit & Compliance

### Enrollment Audit Logs
- Every enrollment action is recorded with:
  - User ID and type (student, professor, system)
  - Action performed
  - Old and new status
  - Comments and metadata
  - Timestamp

### Audit Reports
- Filter by date range, action type, user type
- Paginated results (50 per page)
- Status transition tracking
- Action frequency statistics

### Export
- CSV export of audit reports
- Configurable date range filtering

### System Health Monitoring
- Database connectivity check
- Cache health verification
- Disk space monitoring
- Memory usage tracking
- Enrollment statistics (active, pending, approved)

---

## 9. Security Features

### Authentication
- Unified login page with automatic role detection
- Separate authentication guards per role
- Session-based authentication with remember tokens

### Rate Limiting
- **Login:** Prevents brute-force attacks
- **Enrollment:** 10 operations per minute per user
- Automatic rate limit reset on successful operations

### Middleware Protection
- `AdminAuth` — Admin route protection
- `StudentAuth` — Student route protection
- `ProfessorAuth` — Professor route protection
- `CheckPaymentStatus` — Payment verification before enrollment
- `RateLimitEnrollment` — Enrollment operation throttling
- `RateLimitLogin` — Login attempt throttling
- `SanitizeInput` — Input sanitization
- `LogRequests` — Request logging for audit trail

### Data Protection
- Input sanitization on all requests
- Parameterized queries (Eloquent ORM)
- CSRF token verification
- Hashed passwords (bcrypt/argon2)

---

## 10. Student Classification System

### Classification Logic
- **Regular:** Student has no failed courses → automatic enrollment
- **Irregular:** Student has one or more failed courses → manual enrollment

### Workflow Routing
- System automatically detects classification
- Routes student to appropriate enrollment interface
- Provides recommended actions based on status
- Identifies courses needing retake

### Caching
- Classification results cached for 1 hour
- Cache invalidated on grade submission or student data changes

---

## 11. Performance Optimization

### Caching Strategy
- Prerequisite check results cached per student/course pair
- Student classification cached with 1-hour TTL
- Active courses cached by school
- Model-level cache invalidation on data changes

### Database Optimization
- Performance indexes on frequently queried columns
- Eager loading to prevent N+1 queries
- Connection pooling for MySQL (configurable min/max)
- Query scopes for reusable filtering logic

### Monitoring
- Operation timing with automatic slow-operation detection (>1 second)
- Performance metrics logged to dedicated channel
- System health endpoint for monitoring tools
- Daily slow operation reports cached for 7 days

---

## 12. Course Management

### Course Catalog
- Searchable by course code or title
- Filterable by school, year level, semester
- Active/inactive status control
- Unit credit assignment

### Prerequisites
- Many-to-many prerequisite relationships
- Recursive prerequisite validation
- Cached prerequisite checks
- Dependent course tracking (which courses require this one)

### Professor Assignments
- Many-to-many course-professor relationships
- Role-based assignments (primary instructor, assistant)
- Schedule-based teaching assignments
