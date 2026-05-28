# University Enrollment System v2.0

A Laravel-based enrollment management system for universities. Supports multi-role authentication (Admin, Professor, Student), course management, curriculum templates, professor grading, enrollment workflows, payment tracking, schedule assignment, and professor-assisted enrollment review.

---

## License

**PROPRIETARY SOFTWARE — PRIVATE USE ONLY**

This software is licensed exclusively for use by contracted businesses and authorized individuals. Unauthorized copying, distribution, modification, or use of this software, in whole or in part, is strictly prohibited without prior written consent from the copyright holder.

By using this software, you agree to the following terms:
- This software may only be used by the licensed party and their authorized personnel.
- Redistribution, sublicensing, or sharing of this software with third parties is not permitted.
- Reverse engineering, decompilation, or disassembly of this software is prohibited.
- The software is provided "as is" without warranty of any kind.

For licensing inquiries, contact the development team.

---

## Features

### Admin Panel
- **Dashboard** — System overview (students, professors, enrollments, payments, revenue)
- **Professor Management** — Add, edit, delete professors; designate enrollment assistants
- **Student Management** — Add, edit, delete student accounts
- **Course Management** — Create courses with prerequisites, year level, and semester
- **Schedule Management** — Assign class schedules (subject, professor, day, time, room) with conflict detection
- **Curriculum Templates** — Define default schedules for regular students per school/year/semester; linked to real professor schedules
- **Course-Professor Assignments** — Assign professors to courses as instructor or assistant
- **Enrollment Assistants** — Designate professors to assist in enrollment review
- **Payment Management** — Manually record payments (enrollment, tuition, miscellaneous) with semester and timestamp
- **Payment Confirmation/Rejection** — Approve or reject pending student payments
- **Enrollment Override** — Admin can approve/reject enrollments regardless of professor decision

### Professor Portal
- **Dashboard** — View assigned enrollments pending review
- **My Schedule** — View all assigned class schedules grouped by day
- **Grading** — Submit numeric grades (1.0–5.0) for students enrolled in their courses; grades sync to student records and affect classification
- **Enrollment Review** — Approve or reject student enrollment submissions
- **Audit Reports** — View enrollment audit trails and statistics

### Student Portal
- **Dashboard** — View enrollment status, schedule, and academic progress
- **Regular Enrollment** — Auto-assigned schedule from curriculum templates based on year level and semester
- **Irregular Enrollment** — Manual course selection with real-time validation; pulls available schedules from admin-assigned data
- **Schedule View** — View current class schedule with export options (PDF, CSV)
- **Finances** — View payment history and outstanding balances
- **Payment Required Gate** — Students must have enrollment fee confirmed by admin before accessing enrollment

### Student Classification
- **Regular** — Student has no failed courses; follows the default curriculum template schedule
- **Irregular** — Student has at least one failed course (grade > 3.0); must manually select courses
- Classification updates automatically when professors submit grades

### System Features
- Multi-guard authentication (admin, professor, student)
- Dark/light theme toggle
- Rate limiting on login and enrollment actions
- Input sanitization middleware
- Enrollment audit logging
- Performance monitoring and caching
- Schedule conflict detection (professor time + room conflicts)
- Prerequisite validation
- Automatic student classification based on academic record

---

## Tech Stack

- **Framework:** Laravel 12
- **PHP:** 8.2+
- **Database:** SQLite (default, configurable to MySQL)
- **Frontend:** Blade templates, Vite, CSS (custom theme system)
- **Authentication:** Multi-guard session-based
- **Timezone:** Asia/Manila

---

## Quick Setup (Batch File)

For Windows users, batch files are provided to automate the entire setup and startup process.

### First Time Setup

Double-click **`setup.bat`** — it will:
1. Detect if XAMPP, WAMP, or Laragon is installed (offers to install XAMPP if none found)
2. Check PHP, Composer, and Node.js (installs missing ones automatically)
3. Install all PHP and npm dependencies (skips if already installed)
4. Configure the environment (.env, app key, database)
5. Run migrations and seed the database with test data
6. Window stays open so you can review the output

### Running the Application

Double-click **`start.bat`** — it will:
1. Run pre-flight checks (PHP, dependencies, database)
2. Detect if a server is already running on port 8080 (informs you instead of starting a duplicate)
3. Start the Laravel development server on port 8080
4. Open the application in your default browser

---

## Manual Setup

### Prerequisites
- PHP 8.2+ (included with XAMPP/WAMP/Laragon)
- Composer (https://getcomposer.org)
- Node.js & npm (https://nodejs.org)

### Step-by-Step Installation

```bash
# 1. Clone the repository
git clone https://github.com/Theo-retical808/enrollment_system.git
cd enrollment_system

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install

# 4. Copy environment file
copy .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Create SQLite database (if it doesn't exist)
# On Windows:
type nul > database\database.sqlite
# On Mac/Linux:
# touch database/database.sqlite

# 7. Run migrations and seed the database
php artisan migrate --seed

# 8. Build frontend assets
npm run build

# 9. Start the development server
php artisan serve --port=8080
```

The application will be available at `http://127.0.0.1:8080`.

### Using MySQL Instead of SQLite

If you prefer MySQL (via XAMPP/WAMP):

1. Start Apache and MySQL from your XAMPP/WAMP control panel
2. Create a database named `enrollment_system` in phpMyAdmin
3. Edit `.env` and update:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=enrollment_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Run `php artisan migrate --seed`

---

## Test Accounts

### Admin
| ID | Password |
|---|---|
| ADMIN001 | password |

### Professors (Enrollment Assistants)
| ID | Name | School | Password |
|---|---|---|---|
| PROF001 | John Smith | Computer Science | password |
| PROF002 | Sarah Johnson | Engineering | password |
| PROF003 | Michael Davis | Business Administration | password |

### Professors (Regular — not assisting enrollment)
| ID | Name | School | Password |
|---|---|---|---|
| PROF004 | Emily Wilson | Arts & Sciences | password |
| PROF005 | Carlos Garcia | Computer Science | password |
| PROF006 | Ana Martinez | Engineering | password |

### Students (Regular)
| ID | Name | Year | Password |
|---|---|---|---|
| 2024-REG-001 | Maria Santos | 2 | password |
| 2024-REG-002 | Juan Dela Cruz | 3 | password |

### Students (Irregular)
| ID | Name | Year | Password |
|---|---|---|---|
| 2024-IRR-001 | Pedro Gonzales | 2 | password |
| 2024-IRR-002 | Ana Mercado | 3 | password |

### Additional Students
| ID | Name | Year | Password |
|---|---|---|---|
| 2024-001 | John Doe | 2 | password |
| 2024-002 | Jane Smith | 1 | password |
| 2024-003 | Bob Wilson | 3 | password |
| 2024-004 | Alice Brown | 2 | password |

---

## Grading System

Professors can submit grades for students enrolled in their courses:

| Grade | Description | Status |
|---|---|---|
| 1.0 | Excellent | Passed |
| 1.25 – 1.75 | Very Good | Passed |
| 2.0 – 2.5 | Good / Satisfactory | Passed |
| 2.75 – 3.0 | Passing | Passed |
| 5.0 | Failed | Failed |

- Grades 1.0 – 3.0 = **Passed**
- Grades above 3.0 = **Failed** → student becomes **irregular**

When a professor submits a failing grade, the student's classification is automatically updated.

---

## Payment Workflow

1. Student visits the enrollment page and sees "Payment Required"
2. Student goes to the admin/cashier's office
3. Admin records the payment via the admin panel (Student No., Amount, Payment For, Semester)
4. Payment is recorded as "paid" with the current date and time (Asia/Manila timezone)
5. Student can now access enrollment features

Payment types: `Enrollment`, `Tuition`, `Miscellaneous`

---

## Enrollment Workflow

1. Student logs in and checks payment status
2. If enrollment fee is confirmed by admin, student accesses enrollment
3. System checks student classification:
   - **Regular** (no failed courses) → auto-assigned schedule from curriculum templates
   - **Irregular** (has failed courses) → manual course selection with real-time validation
4. Student reviews and submits enrollment for professor review
5. Professor (designated enrollment assistant) reviews and approves/rejects
6. Admin can override any enrollment decision

---

## Curriculum Templates

Admins define default schedules for regular students:
- Configured per **school**, **year level**, and **semester**
- Each entry links a course to a specific professor schedule (day, time, room)
- When a regular student enrolls, they automatically receive the full schedule from the template
- If no template exists, the system falls back to hardcoded defaults

---

## Schedule Management

Admins assign class schedules with:
- **Subject** — the course to be taught
- **Professor** — who teaches it
- **Day** — Monday through Saturday
- **Time** — start and end time
- **Room** — classroom/lab assignment

The system automatically detects:
- Professor time conflicts (same professor, overlapping times on same day)
- Room conflicts (same room, overlapping times on same day)

Professors can view their assigned schedules in the "My Schedule" tab.
Irregular students see these real schedules when selecting courses during enrollment.

---

## Project Structure

```
app/
├── Console/Commands/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php
│   │   ├── AdminDashboardController.php
│   │   ├── AdminManagementController.php
│   │   ├── ProfessorDashboardController.php
│   │   ├── ProfessorGradingController.php
│   │   ├── ProfessorReviewController.php
│   │   ├── PaymentController.php
│   │   ├── RegularEnrollmentController.php
│   │   ├── IrregularEnrollmentController.php
│   │   └── ...
│   └── Middleware/
├── Models/
│   ├── Admin.php
│   ├── Course.php
│   ├── CourseSchedule.php
│   ├── CurriculumTemplate.php
│   ├── Enrollment.php
│   ├── Payment.php
│   ├── Professor.php
│   ├── School.php
│   └── Student.php
├── Providers/
└── Services/
    ├── RegularStudentEnrollmentService.php
    ├── IrregularStudentEnrollmentService.php
    ├── PaymentVerificationService.php
    ├── ScheduleValidationService.php
    ├── StudentClassificationService.php
    └── ...

database/
├── migrations/
├── seeders/
│   ├── SchoolSeeder.php
│   ├── CourseSeeder.php
│   ├── ProfessorSeeder.php (6 professors, 3 enrollment assistants)
│   ├── StudentSeeder.php
│   ├── CourseScheduleSeeder.php (26 schedules)
│   ├── CurriculumTemplateSeeder.php (23 entries for CS)
│   ├── ComprehensiveCurriculumSeeder.php
│   ├── AdminSeeder.php
│   └── DatabaseSeeder.php
└── factories/

resources/views/
├── admin/
│   ├── curriculum/          # Curriculum template management
│   ├── schedules/           # Schedule management
│   ├── assignments/         # Course-professor assignments
│   ├── courses/
│   ├── professors/
│   ├── students/
│   └── ...
├── professor/
│   ├── dashboard.blade.php
│   ├── schedule.blade.php
│   ├── grading.blade.php
│   └── review-schedule.blade.php
├── student/
└── layouts/
```

---

## Commands

```bash
# Start development server
php artisan serve --port=8080

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# List all routes
php artisan route:list
```

---

## Resetting the Database

To start fresh with clean test data:

```bash
php artisan migrate:fresh --seed
```

This drops all tables, re-runs all migrations, and seeds with test accounts, courses, schedules, curriculum templates, and student data.
