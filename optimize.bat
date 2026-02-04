@echo off
echo ========================================
echo   OPTIMIZING FOR FAST OFFLINE USE
echo ========================================
echo.

cd /d "%~dp0"

echo Clearing all caches...
C:\xampp\php\php.exe artisan config:clear
C:\xampp\php\php.exe artisan cache:clear
C:\xampp\php\php.exe artisan route:clear
C:\xampp\php\php.exe artisan view:clear

echo.
echo Caching for performance...
C:\xampp\php\php.exe artisan config:cache
C:\xampp\php\php.exe artisan route:cache
C:\xampp\php\php.exe artisan view:cache

echo.
echo ✓ Application optimized for fast offline use!
echo ✓ Configuration cached
echo ✓ Routes cached  
echo ✓ Views cached
echo.
echo You can now run start-fast.bat for optimal performance
pause