# Manual Setup Guide

This guide walks you through setting up the Enrollment System manually on a Windows machine. Use this if the automated `setup_run.bat` fails or if you prefer manual control.

---

## Prerequisites

Before you begin, ensure you have the following installed:

| Dependency | Required | Version | Download Link |
|------------|----------|---------|---------------|
| PHP | Yes | 8.2 or higher | https://windows.php.net/download or via XAMPP |
| Composer | Yes | 2.x | https://getcomposer.org/download |
| Node.js | Optional | 18+ LTS | https://nodejs.org |
| Git | Optional | Any | https://git-scm.com |

---

## Step 1: Install PHP

### Option A: Via XAMPP (Recommended for beginners)

1. Download XAMPP from https://www.apachefriends.org
2. Run the installer and install to `C:\xampp`
3. Add PHP to your system PATH:
   - Open **System Properties** → **Environment Variables**
   - Under **System variables**, find `Path` and click **Edit**
   - Add: `C:\xampp\php`
   - Click OK to save
4. Open a new Command Prompt and verify:
   ```
   php -v
   ```

### Option B: Via Laragon

1. Download Laragon from https://laragon.org
2. Install and it will manage PHP, MySQL, and more automatically

### Option C: Standalone PHP

1. Download PHP 8.2+ (VS16 x64 Thread Safe) from https://windows.php.net/download
2. Extract to `C:\php`
3. Copy `php.ini-development` to `php.ini`
4. Edit `php.ini` and enable these extensions (remove the `;` at the start):
   ```ini
   extension=curl
   extension=fileinfo
   extension=mbstring
   extension=openssl
   extension=pdo_sqlite
   extension=tokenizer
   ```
5. Add `C:\php` to your system PATH
6. Verify: `php -v`

### Required PHP Extensions

Ensure these extensions are enabled in your `php.ini`:

- `pdo_sqlite` — SQLite database driver
- `mbstring` — Multibyte string support
- `openssl` — Encryption and HTTPS
- `tokenizer` — PHP tokenizer
- `xml` — XML parsing
- `ctype` — Character type checking
- `json` — JSON encoding/decoding
- `fileinfo` — File information
- `curl` — HTTP requests

To check which extensions are active:
```
php -m
```

---

## Step 2: Install Composer

### Option A: Windows Installer

1. Download the installer from https://getcomposer.org/Composer-Setup.exe
2. Run it — it will detect your PHP installation automatically
3. Verify: `composer --version`

### Option B: Manual Install

```cmd
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=C:\bin --filename=composer
php -r "unlink('composer-setup.php');"
```

Add `C:\bin` to your PATH, then verify: `composer --version`

---

## Step 3: Install Node.js (Optional)

Node.js is only needed if you want to rebuild frontend assets (CSS/JS).

1. Download LTS version from https://nodejs.org
2. Run the installer (includes npm)
3. Verify:
   ```
   node -v
   npm -v
   ```

---

## Step 4: Clone or Download the Project

```cmd
git clone https://github.com/Theo-retical808/enrollment_system.git
cd enrollment_system
```

Or download the ZIP from GitHub and extract it.

---

## Step 5: Install PHP Dependencies

```cmd
composer install --no-interaction --prefer-dist --optimize-autoloader
```

This creates the `vendor/` directory with all Laravel packages.

If you get memory errors:
```cmd
php -d memory_limit=-1 composer.phar install
```

---

## Step 6: Configure Environment

### Create .env file

```cmd
copy .env.example .env
```

### Generate application key

```cmd
php artisan key:generate
```

### Verify .env settings

Open `.env` and ensure these values:
```env
APP_NAME="Enrollment System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

---

## Step 7: Set Up the Database

### Create SQLite file

```cmd
type nul > database\database.sqlite
```

Or on PowerShell:
```powershell
New-Item database\database.sqlite -ItemType File
```

### Run migrations

```cmd
php artisan migrate --force
```

### Seed the database

```cmd
php artisan db:seed --force
```

This populates the database with:
- Schools and departments
- Sample courses with prerequisites
- Professor accounts
- Student accounts
- Admin accounts
- Course schedules
- Curriculum templates

---

## Step 8: Install Frontend Assets (Optional)

```cmd
npm install
npm run build
```

This compiles CSS and JavaScript into `public/build/`.

---

## Step 9: Clear Caches

```cmd
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Or all at once:
```cmd
php artisan optimize:clear
```

---

## Step 10: Create Storage Link

```cmd
php artisan storage:link
```

---

## Step 11: Start the Application

```cmd
php artisan serve --port=8080
```

Open your browser to: **http://127.0.0.1:8080**

---

## Default Login Credentials

| Role | ID | Password |
|------|-----|----------|
| Admin | ADMIN001 | password |
| Professor | PROF001 | password |
| Student | 2024-001 | password |

---

## Troubleshooting

### "PHP is not recognized as an internal or external command"

PHP is not in your system PATH. Add the PHP directory to your PATH environment variable and restart your terminal.

### "Could not find driver" (SQLite error)

The `pdo_sqlite` extension is not enabled. Edit your `php.ini`:
```ini
extension=pdo_sqlite
```

### "The stream or file storage/logs/laravel.log could not be opened"

Storage directories need to be writable:
```cmd
mkdir storage\logs
mkdir storage\framework\cache
mkdir storage\framework\sessions
mkdir storage\framework\views
```

### "Class not found" errors

Regenerate the autoloader:
```cmd
composer dump-autoload
```

### "SQLSTATE: no such table"

Migrations haven't been run:
```cmd
php artisan migrate:fresh --seed --force
```

### Port 8080 already in use

Either stop the other process or use a different port:
```cmd
php artisan serve --port=8081
```

### Frontend assets not loading (unstyled pages)

Rebuild assets:
```cmd
npm run build
```

If npm is not available, the app will still function but may have limited styling.

### Composer memory limit error

```cmd
php -d memory_limit=-1 composer.phar install
```

### "Your requirements could not be resolved"

Try updating Composer and clearing its cache:
```cmd
composer self-update
composer clear-cache
composer install
```

---

## Quick Reference Commands

| Action | Command |
|--------|---------|
| Start server | `php artisan serve --port=8080` |
| Run migrations | `php artisan migrate --force` |
| Seed database | `php artisan db:seed --force` |
| Fresh reset | `php artisan migrate:fresh --seed --force` |
| Clear all caches | `php artisan optimize:clear` |
| Check routes | `php artisan route:list` |
| Interactive shell | `php artisan tinker` |
| Run tests | `php artisan test` |
| Build frontend | `npm run build` |
| Dev server (with Vite) | `composer dev` |

---

## Directory Structure Reference

```
enrollment_system/
├── app/                    # Application code
│   ├── Http/Controllers/   # Request handlers
│   ├── Http/Middleware/     # Request pipeline
│   ├── Models/             # Database models
│   └── Services/           # Business logic
├── bootstrap/              # Framework bootstrap
├── config/                 # Configuration files
├── database/
│   ├── database.sqlite     # SQLite database file
│   ├── migrations/         # Database schema
│   └── seeders/            # Sample data
├── documentation/          # Project documentation
├── public/                 # Web root (index.php)
├── resources/
│   ├── css/                # Stylesheets
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Route definitions
├── storage/                # Logs, cache, sessions
├── .env                    # Environment config
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── setup_run.bat           # Automated setup script
└── start.bat               # Application launcher
```
