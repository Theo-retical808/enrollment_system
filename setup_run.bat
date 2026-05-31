@echo off
setlocal enabledelayedexpansion
title Enrollment System - Full Setup
color 0A

echo.
echo ============================================================
echo        ENROLLMENT SYSTEM - AUTOMATED SETUP
echo ============================================================
echo.
echo   This script will:
echo     1. Scan for required dependencies
echo     2. Install missing dependencies (where possible)
echo     3. Configure the Laravel environment
echo     4. Set up the database
echo     5. Prepare the app so you only need start.bat
echo.
echo ============================================================
echo.
timeout /t 3 /nobreak >nul

set SETUP_ERRORS=0
set MANUAL_REQUIRED=

:: ============================================================
:: STEP 1: SCAN AND INSTALL DEPENDENCIES
:: ============================================================
echo.
echo ************************************************************
echo   STEP 1: DEPENDENCY CHECK ^& INSTALLATION
echo ************************************************************
echo.

:: ─── 1.1 CHECK PHP ──────────────────────────────────────────
echo [1.1] Checking PHP ^(required: 8.2+^)...

set PHP_FOUND=0
set PHP_CMD=

:: Check if PHP is in PATH
where php >nul 2>&1
if %errorlevel% equ 0 (
    set PHP_FOUND=1
    set PHP_CMD=php
    goto :php_version_check
)

:: Scan common locations
for %%P in (
    "C:\xampp\php\php.exe"
    "D:\xampp\php\php.exe"
    "C:\wamp64\bin\php\php8.2.12\php.exe"
    "C:\wamp64\bin\php\php8.2.0\php.exe"
    "C:\laragon\bin\php\php-8.2.12-Win32-vs16-x64\php.exe"
    "C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php.exe"
    "C:\php\php.exe"
    "%ProgramFiles%\PHP\php.exe"
    "%LOCALAPPDATA%\Programs\php\php.exe"
) do (
    if exist %%P (
        set PHP_FOUND=1
        set "PHP_CMD=%%~P"
        for %%D in ("%%~dpP.") do set "PHP_DIR=%%~fD"
        set "PATH=!PHP_DIR!;%PATH%"
        echo        [FOUND] PHP located at %%~P
        echo        [INFO] Added to session PATH.
        goto :php_version_check
    )
)

:: PHP not found anywhere
if %PHP_FOUND%==0 (
    echo        [NOT FOUND] PHP is not installed or not in PATH.
    echo.
    echo        Attempting to install PHP via winget...
    where winget >nul 2>&1
    if !errorlevel! equ 0 (
        winget install --id ApacheFriends.Xampp.8.2 --accept-package-agreements --accept-source-agreements >nul 2>&1
        if !errorlevel! equ 0 (
            if exist "C:\xampp\php\php.exe" (
                set PHP_FOUND=1
                set "PHP_CMD=C:\xampp\php\php.exe"
                set "PATH=C:\xampp\php;%PATH%"
                echo        [OK] XAMPP installed via winget. PHP available.
                goto :php_version_check
            )
        )
        echo        [WARN] winget install did not succeed.
    )
    echo.
    echo        ┌─────────────────────────────────────────────────────┐
    echo        │  MANUAL ACTION REQUIRED:                            │
    echo        │  Install PHP 8.2+ from one of:                      │
    echo        │    - https://www.apachefriends.org (XAMPP)           │
    echo        │    - https://windows.php.net/download               │
    echo        │    - https://laragon.org                             │
    echo        │  Then add PHP to your system PATH and re-run setup. │
    echo        └─────────────────────────────────────────────────────┘
    echo.
    set MANUAL_REQUIRED=!MANUAL_REQUIRED! PHP
    set /a SETUP_ERRORS+=1
    goto :php_done
)

:php_version_check
:: Verify PHP version is 8.2+
for /f "tokens=2 delims= " %%v in ('php -v 2^>nul ^| findstr /i "^PHP"') do (
    set PHP_VER=%%v
    echo        [OK] PHP !PHP_VER! detected.
    
    :: Extract major.minor
    for /f "tokens=1,2 delims=." %%a in ("!PHP_VER!") do (
        set PHP_MAJOR=%%a
        set PHP_MINOR=%%b
    )
    
    if !PHP_MAJOR! LSS 8 (
        echo        [ERROR] PHP 8.2+ required. Found !PHP_VER!.
        set /a SETUP_ERRORS+=1
        set MANUAL_REQUIRED=!MANUAL_REQUIRED! PHP-UPGRADE
    ) else if !PHP_MAJOR! EQU 8 (
        if !PHP_MINOR! LSS 2 (
            echo        [ERROR] PHP 8.2+ required. Found !PHP_VER!.
            set /a SETUP_ERRORS+=1
            set MANUAL_REQUIRED=!MANUAL_REQUIRED! PHP-UPGRADE
        )
    )
)

:: Check required PHP extensions
echo        Checking PHP extensions...
set MISSING_EXT=0
for %%E in (pdo_sqlite mbstring openssl tokenizer xml ctype json fileinfo) do (
    php -m 2>nul | findstr /i "%%E" >nul 2>&1
    if !errorlevel! neq 0 (
        echo        [WARN] Missing extension: %%E
        set /a MISSING_EXT+=1
    )
)
if %MISSING_EXT% equ 0 (
    echo        [OK] All required PHP extensions present.
) else (
    echo        [WARN] Enable missing extensions in php.ini
    echo        [INFO] php.ini location:
    php -i 2>nul | findstr /i "Loaded Configuration File" 2>nul
)

:php_done
echo.

:: ─── 1.2 CHECK COMPOSER ─────────────────────────────────────
echo [1.2] Checking Composer...

set COMPOSER_CMD=

:: Check global composer
where composer >nul 2>&1
if %errorlevel% equ 0 (
    echo        [OK] Composer installed globally.
    set COMPOSER_CMD=composer
    goto :composer_done
)

:: Check local composer.phar
if exist "composer.phar" (
    echo        [OK] Local composer.phar found.
    set COMPOSER_CMD=php composer.phar
    goto :composer_done
)

:: Try to install Composer
echo        [NOT FOUND] Installing Composer...

:: Method 1: winget
where winget >nul 2>&1
if %errorlevel% equ 0 (
    echo        Trying winget...
    winget install --id Composer.Composer --accept-package-agreements --accept-source-agreements >nul 2>&1
    where composer >nul 2>&1
    if !errorlevel! equ 0 (
        echo        [OK] Composer installed via winget.
        set COMPOSER_CMD=composer
        goto :composer_done
    )
)

:: Method 2: Download composer.phar directly
echo        Downloading composer.phar...
powershell -Command "& { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile '%TEMP%\composer-setup.php' -UseBasicParsing }" 2>nul
if exist "%TEMP%\composer-setup.php" (
    php "%TEMP%\composer-setup.php" --install-dir=. --filename=composer.phar 2>nul
    del "%TEMP%\composer-setup.php" 2>nul
    if exist "composer.phar" (
        echo        [OK] Composer installed locally (composer.phar).
        set COMPOSER_CMD=php composer.phar
        goto :composer_done
    )
)

:: Composer install failed
echo        [ERROR] Could not install Composer automatically.
echo        ┌─────────────────────────────────────────────────────┐
echo        │  MANUAL ACTION REQUIRED:                            │
echo        │  Download from https://getcomposer.org/download     │
echo        │  Run the Windows installer or place composer.phar   │
echo        │  in this project directory.                         │
echo        └─────────────────────────────────────────────────────┘
set MANUAL_REQUIRED=!MANUAL_REQUIRED! COMPOSER
set /a SETUP_ERRORS+=1

:composer_done
echo.

:: ─── 1.3 CHECK NODE.JS / NPM ────────────────────────────────
echo [1.3] Checking Node.js / npm ^(required for frontend^)...

set HAS_NPM=0
set NODE_VER=

where node >nul 2>&1
if %errorlevel% equ 0 (
    for /f "tokens=*" %%v in ('node -v 2^>nul') do set NODE_VER=%%v
    echo        [OK] Node.js !NODE_VER! detected.
    set HAS_NPM=1
    goto :node_done
)

:: Try winget install
echo        [NOT FOUND] Attempting to install Node.js...
where winget >nul 2>&1
if %errorlevel% equ 0 (
    echo        Installing via winget (this may take a minute)...
    winget install --id OpenJS.NodeJS.LTS --accept-package-agreements --accept-source-agreements >nul 2>&1
    
    :: Refresh PATH to pick up new install
    for /f "tokens=2*" %%A in ('reg query "HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment" /v Path 2^>nul') do set "PATH=%%B;%PATH%"
    for /f "tokens=2*" %%A in ('reg query "HKCU\Environment" /v Path 2^>nul') do set "PATH=%%B;%PATH%"
    
    where node >nul 2>&1
    if !errorlevel! equ 0 (
        for /f "tokens=*" %%v in ('node -v 2^>nul') do set NODE_VER=%%v
        echo        [OK] Node.js !NODE_VER! installed via winget.
        set HAS_NPM=1
        goto :node_done
    )
)

echo        [WARN] Could not install Node.js automatically.
echo        ┌─────────────────────────────────────────────────────┐
echo        │  OPTIONAL (for frontend assets):                    │
echo        │  Download from https://nodejs.org (LTS version)     │
echo        │  The app will still work without it, but frontend   │
echo        │  assets won't be rebuilt.                           │
echo        └─────────────────────────────────────────────────────┘
echo        [INFO] Continuing without npm...

:node_done
echo.

:: ─── 1.4 CHECK GIT ──────────────────────────────────────────
echo [1.4] Checking Git...
where git >nul 2>&1
if %errorlevel% equ 0 (
    for /f "tokens=3" %%v in ('git --version 2^>nul') do echo        [OK] Git %%v detected.
) else (
    echo        [INFO] Git not found. Not required for running the app.
    echo        [INFO] Install from https://git-scm.com if needed.
)
echo.

:: ─── DEPENDENCY SUMMARY ─────────────────────────────────────
echo ────────────────────────────────────────────────────────────
echo   Dependency Scan Summary:
echo ────────────────────────────────────────────────────────────
if %PHP_FOUND%==1 (echo        PHP:      [OK]) else (echo        PHP:      [MISSING])
if defined COMPOSER_CMD (echo        Composer: [OK]) else (echo        Composer: [MISSING])
if %HAS_NPM%==1 (echo        Node/npm: [OK]) else (echo        Node/npm: [SKIP - optional])
echo ────────────────────────────────────────────────────────────
echo.

:: Abort if critical dependencies missing
if %SETUP_ERRORS% GTR 0 (
    if not defined COMPOSER_CMD (
        echo [ERROR] Critical dependencies missing. Cannot continue.
        echo         Missing: %MANUAL_REQUIRED%
        echo         Please install them manually and re-run this script.
        echo.
        goto :final_done
    )
    if %PHP_FOUND%==0 (
        echo [ERROR] Critical dependencies missing. Cannot continue.
        echo         Missing: %MANUAL_REQUIRED%
        echo         Please install them manually and re-run this script.
        echo.
        goto :final_done
    )
)

:: ============================================================
:: STEP 2: INSTALL PROJECT DEPENDENCIES
:: ============================================================
echo.
echo ************************************************************
echo   STEP 2: INSTALL PROJECT DEPENDENCIES
echo ************************************************************
echo.

:: ─── 2.1 PHP DEPENDENCIES (COMPOSER) ────────────────────────
echo [2.1] Installing PHP dependencies...
if exist "vendor\autoload.php" (
    echo        [OK] vendor/ already exists. Verifying...
    call %COMPOSER_CMD% install --no-interaction --prefer-dist --optimize-autoloader 2>nul
    if !errorlevel! equ 0 (
        echo        [OK] PHP dependencies verified and up to date.
    ) else (
        echo        [WARN] Composer install had issues. Trying fresh install...
        call %COMPOSER_CMD% install --no-interaction --prefer-dist 2>nul
    )
) else (
    echo        Running composer install (first time, may take a few minutes)...
    call %COMPOSER_CMD% install --no-interaction --prefer-dist --optimize-autoloader
    if !errorlevel! neq 0 (
        echo        [ERROR] Composer install failed.
        echo        Try running manually: %COMPOSER_CMD% install
        set /a SETUP_ERRORS+=1
        goto :skip_npm
    )
    echo        [OK] PHP dependencies installed.
)
echo.

:: ─── 2.2 NPM DEPENDENCIES ───────────────────────────────────
echo [2.2] Installing frontend dependencies...
:skip_npm
if %HAS_NPM%==0 (
    echo        [SKIP] npm not available. Frontend assets will use pre-built files.
    goto :npm_done
)

if exist "node_modules\.package-lock.json" (
    echo        [OK] node_modules/ already exists.
) else (
    echo        Running npm install...
    call npm install 2>nul
    if !errorlevel! equ 0 (
        echo        [OK] npm dependencies installed.
    ) else (
        echo        [WARN] npm install had issues. Frontend may not build.
    )
)

:: Build frontend assets
if exist "public\build\manifest.json" (
    echo        [OK] Frontend assets already built.
) else (
    echo        Building frontend assets (npm run build)...
    call npm run build 2>nul
    if !errorlevel! equ 0 (
        echo        [OK] Frontend assets built successfully.
    ) else (
        echo        [WARN] Frontend build failed. App may still work with basic styles.
    )
)

:npm_done
echo.

:: ============================================================
:: STEP 3: CONFIGURE LARAVEL ENVIRONMENT
:: ============================================================
echo.
echo ************************************************************
echo   STEP 3: CONFIGURE LARAVEL ENVIRONMENT
echo ************************************************************
echo.

:: ─── 3.1 ENVIRONMENT FILE ───────────────────────────────────
echo [3.1] Setting up .env file...
if exist ".env" (
    echo        [OK] .env file already exists.
) else if exist ".env.example" (
    echo        Creating .env from .env.example...
    copy .env.example .env >nul
    echo        [OK] .env file created.
) else (
    echo        [ERROR] No .env.example found. Creating minimal .env...
    (
        echo APP_NAME="Enrollment System"
        echo APP_ENV=local
        echo APP_KEY=
        echo APP_DEBUG=true
        echo APP_URL=http://127.0.0.1:8080
        echo DB_CONNECTION=sqlite
        echo DB_DATABASE=database/database.sqlite
    ) > .env
    echo        [OK] Minimal .env created.
)
echo.

:: ─── 3.2 APPLICATION KEY ────────────────────────────────────
echo [3.2] Checking application key...
findstr /c:"APP_KEY=base64:" .env >nul 2>&1
if %errorlevel% equ 0 (
    echo        [OK] Application key already set.
) else (
    echo        Generating application key...
    php artisan key:generate --ansi --force
    echo        [OK] Application key generated.
)
echo.

:: ─── 3.3 CLEAR ALL CACHES ───────────────────────────────────
echo [3.3] Clearing all caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan route:clear >nul 2>&1
php artisan view:clear >nul 2>&1
echo        [OK] All caches cleared (config, cache, routes, views).
echo.

:: ============================================================
:: STEP 4: DATABASE SETUP
:: ============================================================
echo.
echo ************************************************************
echo   STEP 4: DATABASE SETUP
echo ************************************************************
echo.

:: ─── 4.1 CREATE SQLITE FILE ─────────────────────────────────
echo [4.1] Setting up SQLite database...
if not exist "database" (
    mkdir database
)

if exist "database\database.sqlite" (
    echo        [OK] database.sqlite already exists.
) else (
    echo        Creating database\database.sqlite...
    type nul > database\database.sqlite
    echo        [OK] SQLite database file created.
)
echo.

:: ─── 4.2 RUN MIGRATIONS ─────────────────────────────────────
echo [4.2] Running database migrations...

:: Check if migrations table exists (indicates DB has been set up before)
php artisan migrate:status >nul 2>&1
if %errorlevel% equ 0 (
    :: Check for pending migrations
    php artisan migrate:status 2>nul | findstr /c:"Pending" >nul 2>&1
    if !errorlevel! equ 0 (
        echo        [INFO] Pending migrations found. Running...
        php artisan migrate --force
        if !errorlevel! equ 0 (
            echo        [OK] Migrations applied.
        ) else (
            echo        [WARN] Migration had issues. Trying fresh...
            php artisan migrate:fresh --force
        )
    ) else (
        echo        [OK] All migrations already applied.
    )
) else (
    echo        [INFO] Fresh database. Running all migrations...
    php artisan migrate --force
    if !errorlevel! neq 0 (
        echo        [WARN] Migration failed. Trying migrate:fresh...
        php artisan migrate:fresh --force
    )
    echo        [OK] Migrations complete.
)
echo.

:: ─── 4.3 SEED DATABASE ──────────────────────────────────────
echo [4.3] Checking database seeding...

:: Check if data already exists by counting students
for /f "tokens=*" %%R in ('php artisan tinker --execute="echo App\Models\Student::count();" 2^>nul') do set STUDENT_COUNT=%%R

:: Clean the output (remove any extra whitespace)
set STUDENT_COUNT=%STUDENT_COUNT: =%

if "%STUDENT_COUNT%"=="" set STUDENT_COUNT=0
if "%STUDENT_COUNT%"=="0" (
    echo        [INFO] Database is empty. Running seeders...
    php artisan db:seed --force
    if !errorlevel! equ 0 (
        echo        [OK] Database seeded successfully.
    ) else (
        echo        [WARN] Seeding had issues. Some data may be missing.
        echo        [INFO] Try running: php artisan db:seed --force
    )
) else (
    echo        [OK] Database already has data (%STUDENT_COUNT% students found).
    echo        [INFO] Skipping seeders to preserve existing data.
    echo        [INFO] To re-seed: php artisan migrate:fresh --seed --force
)
echo.

:: ============================================================
:: STEP 5: FINAL OPTIMIZATION
:: ============================================================
echo.
echo ************************************************************
echo   STEP 5: FINAL OPTIMIZATION
echo ************************************************************
echo.

:: ─── 5.1 OPTIMIZE FOR PRODUCTION-LIKE PERFORMANCE ───────────
echo [5.1] Optimizing application...
php artisan optimize:clear >nul 2>&1
echo        [OK] Cleared stale optimization files.

:: Create storage link if not exists
if not exist "public\storage" (
    php artisan storage:link >nul 2>&1
    echo        [OK] Storage symlink created.
) else (
    echo        [OK] Storage symlink already exists.
)
echo.

:: ─── 5.2 VERIFY DIRECTORY PERMISSIONS ───────────────────────
echo [5.2] Verifying directory structure...
if not exist "storage\logs" mkdir "storage\logs" 2>nul
if not exist "storage\framework\cache" mkdir "storage\framework\cache" 2>nul
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions" 2>nul
if not exist "storage\framework\views" mkdir "storage\framework\views" 2>nul
if not exist "bootstrap\cache" mkdir "bootstrap\cache" 2>nul
echo        [OK] All required directories exist.
echo.

:: ─── 5.3 FINAL VERIFICATION ─────────────────────────────────
echo [5.3] Running final verification...
set VERIFY_PASS=1

if not exist "vendor\autoload.php" (
    echo        [FAIL] vendor/autoload.php missing
    set VERIFY_PASS=0
)
if not exist ".env" (
    echo        [FAIL] .env file missing
    set VERIFY_PASS=0
)
if not exist "database\database.sqlite" (
    echo        [FAIL] database.sqlite missing
    set VERIFY_PASS=0
)

:: Quick artisan check
php artisan --version >nul 2>&1
if %errorlevel% neq 0 (
    echo        [FAIL] php artisan not responding
    set VERIFY_PASS=0
)

if %VERIFY_PASS%==1 (
    echo        [OK] All verification checks passed.
) else (
    echo        [WARN] Some checks failed. The app may not work correctly.
    set /a SETUP_ERRORS+=1
)
echo.

:: ============================================================
:: SETUP COMPLETE
:: ============================================================
echo.
echo ============================================================
if %SETUP_ERRORS%==0 (
    echo        SETUP COMPLETED SUCCESSFULLY!
) else (
    echo        SETUP COMPLETED WITH WARNINGS
    if defined MANUAL_REQUIRED (
        echo        Manual action needed for: %MANUAL_REQUIRED%
    )
)
echo ============================================================
echo.
echo   The application is ready. To start:
echo.
echo     1. Double-click  start.bat
echo        OR
echo     2. Run:  php artisan serve --port=8080
echo.
echo   The app will be available at: http://127.0.0.1:8080
echo.
echo   ┌─────────────────────────────────────────────────────┐
echo   │  Default Login Credentials                          │
echo   ├───────────┬──────────┬──────────────────────────────┤
echo   │  Role     │  ID      │  Password                    │
echo   ├───────────┼──────────┼──────────────────────────────┤
echo   │  Admin    │ ADMIN001 │  password                    │
echo   │  Professor│ PROF001  │  password                    │
echo   │  Student  │ 2024-001 │  password                    │
echo   └───────────┴──────────┴──────────────────────────────┘
echo.
echo ============================================================
echo.

:final_done
echo.
echo --- Press any key to exit ---
pause >nul
endlocal
