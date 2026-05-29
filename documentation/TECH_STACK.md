# Technology Stack

## Overview

This is a **University Enrollment Management System** built with the Laravel PHP framework. It handles student enrollment workflows, payment processing, grading, scheduling, and administrative management for an academic institution.

---

## Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | ^8.2 | Server-side language |
| Laravel Framework | ^12.0 | MVC web application framework |
| Laravel Tinker | ^2.10.1 | Interactive REPL for debugging |

### Database

- **Default:** SQLite (file-based, zero-config)
- **Production-ready support:** MySQL, MariaDB, PostgreSQL, SQL Server
- **Caching layer:** Redis (with phpredis client)
- **Connection pooling:** Configurable min/max connections for MySQL

### Authentication

- Multi-guard system with separate authentication for:
  - Students (`student` guard)
  - Professors (`professor` guard)
  - Admins (`admin` guard)
- Password hashing via Laravel's built-in `hashed` cast
- Session-based authentication

---

## Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| Vite | ^7.0.7 | Build tool and dev server |
| Laravel Vite Plugin | ^1.3.0 | Laravel-Vite integration |
| Axios | ^1.11.0 | HTTP client for AJAX requests |
| Blade Templates | (built-in) | Server-side templating engine |

---

## Development Tools

| Tool | Version | Purpose |
|------|---------|---------|
| Laravel Sail | ^1.41 | Docker-based local development |
| Laravel Pint | ^1.24 | PHP code style fixer (PSR-12) |
| Laravel Pail | ^1.2.2 | Real-time log viewer |
| Concurrently | ^9.0.1 | Run multiple dev processes |
| PHPUnit | ^11.5.50 | Unit and feature testing |
| Mockery | ^1.6 | Mock objects for testing |
| FakerPHP | ^1.23 | Fake data generation for seeding/testing |
| Collision | ^8.6 | Beautiful error reporting |

---

## Architecture

### Design Pattern
- **MVC** (Model-View-Controller) with a **Service Layer** pattern
- Controllers delegate business logic to dedicated service classes
- Models use Eloquent ORM with relationships, scopes, and caching

### Key Architectural Decisions

1. **Service Layer:** Business logic is encapsulated in service classes (`app/Services/`) rather than controllers
2. **Multi-Guard Auth:** Separate authentication guards for each user role
3. **Middleware Pipeline:** Security, rate limiting, and payment verification handled via middleware
4. **Event-Driven Logging:** Comprehensive audit logging through dedicated logger services
5. **Cache-First Strategy:** Expensive queries (prerequisites, student classification) are cached with TTL
6. **Database Transactions:** Critical operations (enrollment submission, grading) use DB transactions

### Directory Structure

```
app/
├── Console/Commands/       # Artisan commands (system health monitoring)
├── Exceptions/             # Custom exception classes
├── Http/
│   ├── Controllers/        # Request handling (16 controllers)
│   └── Middleware/         # Request pipeline (8 middleware)
├── Models/                 # Eloquent models (12 models)
├── Providers/              # Service providers
└── Services/               # Business logic services (7 services)
```

---

## Infrastructure

### Caching
- Redis for session, cache, and queue management
- Model-level cache invalidation on save/delete
- TTL-based caching for expensive queries (1 hour default)

### Logging
- Custom `enrollment` channel for enrollment-specific events
- Custom `performance` channel for operation timing
- Structured logging with context (student ID, timestamps, IP addresses)

### Queue System
- Laravel Queue with configurable driver
- Used for background processing of notifications and reports

---

## Setup & Deployment

### Quick Start
```bash
composer setup
```
This runs: install dependencies → generate .env → generate app key → run migrations → install npm packages → build assets.

### Development Server
```bash
composer dev
```
Runs concurrently: Laravel server, queue listener, Pail log viewer, and Vite dev server.

### Testing
```bash
composer test
```
Clears config cache and runs PHPUnit test suite.

---

## Security Stack

- CSRF protection (Laravel default)
- Input sanitization middleware
- Rate limiting (login + enrollment operations)
- Parameterized queries via Eloquent ORM
- Password hashing (bcrypt/argon2)
- Guard-based route protection
- Payment verification before enrollment access
