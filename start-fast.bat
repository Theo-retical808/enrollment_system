@echo off
echo ========================================
echo   STUDENT ENROLLMENT SYSTEM - FAST MODE
echo ========================================
echo.
echo Starting optimized Laravel server...
echo.

cd /d "%~dp0"

REM Check if XAMPP is running
echo Checking XAMPP services...
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo ✓ Apache is running
) else (
    echo ✗ Apache is not running - Please start XAMPP first!
    pause
    exit /b 1
)

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo ✓ MySQL is running
) else (
    echo ✗ MySQL is not running - Please start XAMPP first!
    pause
    exit /b 1
)

echo.
echo Starting Laravel development server...
echo ✓ Optimized for offline use
echo ✓ File-based caching enabled
echo ✓ Reduced logging for speed
echo ✓ Fast session handling
echo.
echo Access your application at: http://127.0.0.1:8000
echo Press Ctrl+C to stop the server
echo.

C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000

pause