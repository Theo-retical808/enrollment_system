@echo off
title Enrollment System - Start
color 0B
echo ============================================================
echo        ENROLLMENT SYSTEM - STARTING
echo ============================================================
echo.

:: ─── PRE-FLIGHT CHECKS ──────────────────────────────────────
echo [1/3] Running pre-flight checks...

:: Check PHP
where php >nul 2>&1
if %errorlevel% neq 0 (
    :: Try XAMPP PHP
    if exist "C:\xampp\php\php.exe" (
        set "PATH=C:\xampp\php;%PATH%"
    ) else if exist "D:\xampp\php\php.exe" (
        set "PATH=D:\xampp\php;%PATH%"
    ) else (
        echo        [ERROR] PHP not found. Run setup.bat first.
        pause
        exit /b 1
    )
)

:: Check vendor folder
if not exist "vendor" (
    echo        [ERROR] Dependencies not installed. Run setup.bat first.
    pause
    exit /b 1
)

:: Check .env
if not exist ".env" (
    echo        [ERROR] .env file missing. Run setup.bat first.
    pause
    exit /b 1
)

:: Check database
if not exist "database\database.sqlite" (
    echo        [ERROR] Database not found. Run setup.bat first.
    pause
    exit /b 1
)

echo        [OK] All checks passed.
echo.

:: ─── CHECK IF SERVER IS ALREADY RUNNING ──────────────────────
echo [2/3] Checking for existing server on port 8080...
netstat -ano | findstr ":8080" | findstr "LISTENING" >nul 2>&1
if %errorlevel% equ 0 (
    echo.
    echo        ============================================
    echo        [!] A server is ALREADY RUNNING on port 8080
    echo        ============================================
    echo.
    echo        The application may already be accessible at:
    echo        http://127.0.0.1:8080
    echo.
    echo        To stop the existing server, close its terminal
    echo        window or press Ctrl+C in that window, then
    echo        run this script again.
    echo.
    echo        Or open the app in your browser now:
    start "" "http://127.0.0.1:8080"
    echo.
    pause
    exit /b 0
)
echo        [OK] Port 8080 is available.
echo.

:: ─── START SERVICES IF USING MYSQL ───────────────────────────
echo [3/3] Starting application...

:: Only start XAMPP/WAMP if using MySQL
findstr /c:"DB_CONNECTION=mysql" .env >nul 2>&1
if %errorlevel% equ 0 (
    echo        MySQL mode detected. Starting local server...
    
    if exist "C:\xampp\xampp_start.exe" (
        echo        Starting XAMPP...
        start "" "C:\xampp\xampp_start.exe"
        timeout /t 3 /nobreak >nul
    ) else if exist "C:\xampp\apache_start.bat" (
        start "" "C:\xampp\apache_start.bat"
        start "" "C:\xampp\mysql_start.bat"
        timeout /t 3 /nobreak >nul
    ) else if exist "D:\xampp\xampp_start.exe" (
        start "" "D:\xampp\xampp_start.exe"
        timeout /t 3 /nobreak >nul
    ) else if exist "C:\wamp64\wampmanager.exe" (
        echo        Starting WAMP...
        start "" "C:\wamp64\wampmanager.exe"
        timeout /t 5 /nobreak >nul
    ) else if exist "C:\laragon\laragon.exe" (
        echo        Starting Laragon...
        start "" "C:\laragon\laragon.exe"
        timeout /t 5 /nobreak >nul
    ) else (
        echo        [WARN] No local server found. Make sure MySQL is running.
    )
) else (
    echo        Using SQLite - no external services needed.
)

echo.
echo ============================================================
echo.
echo   Application starting at: http://127.0.0.1:8080
echo.
echo   Login Credentials:
echo   +-----------+----------+----------+
echo   ^| Role      ^| ID       ^| Password ^|
echo   +-----------+----------+----------+
echo   ^| Admin     ^| ADMIN001 ^| password ^|
echo   ^| Professor ^| PROF001  ^| password ^|
echo   ^| Student   ^| 2024-001 ^| password ^|
echo   +-----------+----------+----------+
echo.
echo   Press Ctrl+C to stop the server.
echo.
echo ============================================================
echo.

:: Open browser after a short delay
start "" "http://127.0.0.1:8080"

:: Start Laravel server
php artisan serve --port=8080
