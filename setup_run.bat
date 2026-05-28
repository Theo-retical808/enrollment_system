@echo off
title Enrollment System - Setup
color 0A
echo ============================================================
echo        ENROLLMENT SYSTEM - ENVIRONMENT SETUP
echo ============================================================
echo.

:: ─── CHECK XAMPP / WAMP / LARAGON ────────────────────────────
echo [1/6] Checking for local server...
set XAMPP_PATH=
set SERVER_NAME=

if exist "C:\xampp\php\php.exe" (
    set XAMPP_PATH=C:\xampp
    set SERVER_NAME=XAMPP
)
if exist "D:\xampp\php\php.exe" (
    set XAMPP_PATH=D:\xampp
    set SERVER_NAME=XAMPP
)
if exist "C:\wamp64\bin\php" (
    set XAMPP_PATH=C:\wamp64
    set SERVER_NAME=WAMP64
)
if exist "C:\laragon\bin\php" (
    set XAMPP_PATH=C:\laragon
    set SERVER_NAME=Laragon
)

if defined SERVER_NAME (
    echo        [OK] %SERVER_NAME% found at %XAMPP_PATH%
) else (
    echo        [NOT FOUND] No XAMPP/WAMP/Laragon detected.
    echo.
    set /p INSTALL_XAMPP="        Install XAMPP automatically? (Y/N): "
    if /i "%INSTALL_XAMPP%"=="Y" (
        echo        Downloading XAMPP installer...
        powershell -Command "& { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://sourceforge.net/projects/xampp/files/XAMPP%%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download' -OutFile '%TEMP%\xampp-installer.exe' -UseBasicParsing }" 2>nul
        if exist "%TEMP%\xampp-installer.exe" (
            echo        Installing XAMPP to C:\xampp ...
            start /wait "" "%TEMP%\xampp-installer.exe" --mode unattended --launchapps 0
            del "%TEMP%\xampp-installer.exe" 2>nul
            if exist "C:\xampp\php\php.exe" (
                set XAMPP_PATH=C:\xampp
                set SERVER_NAME=XAMPP
                echo        [OK] XAMPP installed.
            ) else (
                echo        [WARN] XAMPP install may have failed. Continuing...
            )
        ) else (
            echo        [WARN] Download failed. Install XAMPP manually later.
        )
    ) else (
        echo        Skipping. Install later from https://www.apachefriends.org
    )
)
echo.

:: ─── CHECK PHP ───────────────────────────────────────────────
echo [2/6] Checking PHP...
where php >nul 2>&1
if %errorlevel% equ 0 (
    for /f "tokens=2 delims= " %%v in ('php -v 2^>nul ^| findstr /i "^PHP"') do (
        echo        [OK] PHP %%v already installed.
    )
    goto :php_done
)

:: PHP not in PATH, try to add from XAMPP
if defined XAMPP_PATH (
    if exist "%XAMPP_PATH%\php\php.exe" (
        set "PATH=%XAMPP_PATH%\php;%PATH%"
        echo        [OK] PHP found via %SERVER_NAME%, added to session PATH.
        echo        [NOTE] Add "%XAMPP_PATH%\php" to system PATH for permanent access.
        goto :php_done
    )
)

echo        [ERROR] PHP not found. Install XAMPP or add PHP to PATH.
echo.
echo        Setup cannot continue without PHP.
goto :done

:php_done
echo.

:: ─── CHECK COMPOSER ──────────────────────────────────────────
echo [3/6] Checking Composer...

:: Check global composer first
where composer >nul 2>&1
if %errorlevel% equ 0 (
    echo        [OK] Composer already installed globally.
    set COMPOSER_CMD=composer
    goto :composer_done
)

:: Check local composer.phar
if exist "composer.phar" (
    echo        [OK] Local composer.phar already exists.
    set COMPOSER_CMD=php composer.phar
    goto :composer_done
)

:: Not found - install locally
echo        [NOT FOUND] Installing Composer locally...
powershell -Command "& { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile '%TEMP%\composer-setup.php' -UseBasicParsing }" 2>nul
if exist "%TEMP%\composer-setup.php" (
    php "%TEMP%\composer-setup.php" --install-dir=. --filename=composer.phar
    del "%TEMP%\composer-setup.php" 2>nul
    if exist "composer.phar" (
        echo        [OK] Composer installed locally.
        set COMPOSER_CMD=php composer.phar
    ) else (
        echo        [ERROR] Composer install failed.
        echo        Download from https://getcomposer.org/download/
        goto :done
    )
) else (
    echo        [ERROR] Could not download Composer installer.
    goto :done
)

:composer_done
echo.

:: ─── CHECK NODE / NPM ───────────────────────────────────────
echo [4/6] Checking Node.js / npm...
set HAS_NPM=0

where npm >nul 2>&1
if %errorlevel% equ 0 (
    set HAS_NPM=1
    for /f "tokens=*" %%v in ('node -v 2^>nul') do echo        [OK] Node.js %%v already installed.
    goto :node_done
)

echo        [NOT FOUND] Node.js/npm not in PATH.
echo        Frontend assets require npm. Install from https://nodejs.org
echo        [SKIP] Skipping frontend build.

:node_done
echo.

:: ─── INSTALL DEPENDENCIES ────────────────────────────────────
echo [5/6] Installing dependencies...

:: PHP dependencies
if exist "vendor\autoload.php" (
    echo        [OK] PHP dependencies already installed.
) else (
    echo        Running composer install...
    call %COMPOSER_CMD% install --no-interaction --prefer-dist --optimize-autoloader
    if %errorlevel% neq 0 (
        echo        [ERROR] Composer install failed.
        goto :done
    )
    echo        [OK] PHP dependencies installed.
)

:: NPM dependencies
if "%HAS_NPM%"=="1" (
    if exist "node_modules\.package-lock.json" (
        echo        [OK] npm dependencies already installed.
    ) else (
        echo        Running npm install...
        call npm install 2>nul
        echo        [OK] npm dependencies installed.
    )
    if exist "public\build\manifest.json" (
        echo        [OK] Frontend assets already built.
    ) else (
        echo        Building frontend assets...
        call npm run build 2>nul
        echo        [OK] Frontend build done.
    )
)
echo.

:: ─── CONFIGURE APPLICATION ───────────────────────────────────
echo [6/6] Configuring application...

:: .env file
if exist ".env" (
    echo        [OK] .env file exists.
) else (
    echo        Creating .env from .env.example...
    copy .env.example .env >nul
    echo        [OK] .env created.
)

:: App key
findstr /c:"APP_KEY=base64:" .env >nul 2>&1
if %errorlevel% equ 0 (
    echo        [OK] App key already set.
) else (
    echo        Generating application key...
    php artisan key:generate --ansi --force
)

:: SQLite database
if exist "database\database.sqlite" (
    echo        [OK] SQLite database exists.
) else (
    echo        Creating SQLite database file...
    type nul > database\database.sqlite
)

:: Migrations
php artisan migrate:status >nul 2>&1
if %errorlevel% equ 0 (
    php artisan migrate:status 2>nul | findstr "Pending" >nul 2>&1
    if %errorlevel% equ 0 (
        echo        Running pending migrations...
        php artisan migrate --force
        echo        Seeding database...
        php artisan db:seed --force
    ) else (
        echo        [OK] Database already migrated.
    )
) else (
    echo        Running fresh migration + seed...
    php artisan migrate:fresh --seed --force
)

:: Clear caches
php artisan optimize:clear >nul 2>&1
echo        [OK] Caches cleared.

echo.
echo ============================================================
echo        SETUP COMPLETE!
echo ============================================================
echo.
echo   Everything is ready. Run 'start.bat' to launch the app.
echo.
echo   Test Accounts:
echo   +-----------+----------+----------+
echo   ^| Role      ^| ID       ^| Password ^|
echo   +-----------+----------+----------+
echo   ^| Admin     ^| ADMIN001 ^| password ^|
echo   ^| Professor ^| PROF001  ^| password ^|
echo   ^| Student   ^| 2024-001 ^| password ^|
echo   +-----------+----------+----------+
echo.
echo ============================================================

:done
echo.
echo --- Setup script finished. This window will stay open. ---
