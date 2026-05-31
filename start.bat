@echo off
setlocal enabledelayedexpansion
title Enrollment System - Running
color 0B

echo.
echo ============================================================
echo        ENROLLMENT SYSTEM - STARTING
echo ============================================================
echo.

:: ─── PRE-FLIGHT CHECKS ──────────────────────────────────────
echo [1/4] Running pre-flight checks...

:: Check PHP
set PHP_OK=0
where php >nul 2>&1
if %errorlevel% equ 0 (
    set PHP_OK=1
) else (
    :: Try common locations
    for %%P in ("C:\xampp\php" "D:\xampp\php" "C:\laragon\bin\php\php-8.2.12-Win32-vs16-x64" "C:\php") do (
        if exist "%%~P\php.exe" (
            set "PATH=%%~P;%PATH%"
            set PHP_OK=1
            goto :php_found
        )
    )
)
:php_found

if %PHP_OK%==0 (
    echo        [ERROR] PHP not found. Run setup_run.bat first.
    echo.
    pause
    exit /b 1
)
echo        [OK] PHP available.

:: Check vendor
if not exist "vendor\autoload.php" (
    echo        [ERROR] Dependencies not installed. Run setup_run.bat first.
    pause
    exit /b 1
)
echo        [OK] Dependencies installed.

:: Check .env
if not exist ".env" (
    echo        [ERROR] .env file missing. Run setup_run.bat first.
    pause
    exit /b 1
)
echo        [OK] Environment configured.

:: Check database
if not exist "database\database.sqlite" (
    echo        [ERROR] Database not found. Run setup_run.bat first.
    pause
    exit /b 1
)
echo        [OK] Database ready.
echo.

:: ─── CHECK PORT AVAILABILITY ─────────────────────────────────
echo [2/4] Checking port 8080...
netstat -ano 2>nul | findstr ":8080" | findstr "LISTENING" >nul 2>&1
if %errorlevel% equ 0 (
    echo.
    echo        +---------------------------------------------------+
    echo        ^|  Port 8080 is already in use!                     ^|
    echo        ^|                                                   ^|
    echo        ^|  The app may already be running at:               ^|
    echo        ^|  http://127.0.0.1:8080                            ^|
    echo        ^|                                                   ^|
    echo        ^|  Close the other server first, or the app         ^|
    echo        ^|  is already accessible in your browser.           ^|
    echo        +---------------------------------------------------+
    echo.
    start "" "http://127.0.0.1:8080"
    pause
    exit /b 0
)
echo        [OK] Port 8080 available.
echo.

:: ─── START EXTERNAL SERVICES IF NEEDED ───────────────────────
echo [3/4] Checking external services...

findstr /c:"DB_CONNECTION=mysql" .env >nul 2>&1
if %errorlevel% equ 0 (
    echo        [INFO] MySQL mode detected. Starting local server...
    if exist "C:\xampp\xampp_start.exe" (
        start "" "C:\xampp\xampp_start.exe"
        timeout /t 3 /nobreak >nul
    ) else if exist "D:\xampp\xampp_start.exe" (
        start "" "D:\xampp\xampp_start.exe"
        timeout /t 3 /nobreak >nul
    ) else if exist "C:\laragon\laragon.exe" (
        start "" "C:\laragon\laragon.exe"
        timeout /t 5 /nobreak >nul
    ) else (
        echo        [WARN] No local MySQL server found. Ensure MySQL is running.
    )
) else (
    echo        [OK] Using SQLite - no external services needed.
)
echo.

:: ─── LAUNCH APPLICATION ──────────────────────────────────────
echo [4/4] Launching application...
echo.
echo ============================================================
echo.
echo   Application URL:  http://127.0.0.1:8080
echo.
echo   Login Credentials:
echo   +-----------+----------+------------+
echo   ^| Role      ^| ID       ^| Password   ^|
echo   +-----------+----------+------------+
echo   ^| Admin     ^| ADMIN001 ^| password   ^|
echo   ^| Professor ^| PROF001  ^| password   ^|
echo   ^| Student   ^| 2024-001 ^| password   ^|
echo   +-----------+----------+------------+
echo.
echo   Press Ctrl+C to stop the server.
echo.
echo ============================================================
echo.

:: Open browser
timeout /t 2 /nobreak >nul
start "" "http://127.0.0.1:8080"

:: Start Laravel development server
php artisan serve --port=8080

endlocal
