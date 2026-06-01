@echo off
title Moody's Restaurant - Desktop App
cd /d "%~dp0"

:: Check for PHP
if exist "php\php.exe" (
    set PHP_PATH=%~dp0php\php.exe
) else (
    where php >nul 2>&1
    if %ERRORLEVEL% neq 0 (
        echo.
        echo ============================================
        echo   ERROR: PHP not found!
        echo ============================================
        echo.
        echo Please install PHP 8.2+ or place portable PHP
        echo in the "php" folder next to this script.
        echo.
        echo Download: https://windows.php.net/downloads
        echo.
        pause
        exit /b 1
    )
    set PHP_PATH=php
)

:: Start server
set PORT=8080

echo.
echo ============================================
echo    Moody's Restaurant
echo ============================================
echo.
echo  Server: http://127.0.0.1:%PORT%
echo  Login:  admin@althwq.com / password
echo.
echo  Close this window to stop the server.
echo ============================================
echo.

:: Open browser after a short delay
start /B "" cmd /c "timeout /t 2 /nobreak >nul & start http://127.0.0.1:%PORT%"

:: Start PHP built-in server
"%PHP_PATH%" -S 127.0.0.1:%PORT% -t "%~dp0app\public"

:: Cleanup
taskkill /f /im php.exe >nul 2>&1
