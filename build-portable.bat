@echo off
title Moody's - Build Portable Package
cd /d "%~dp0"

echo ============================================
echo   Building Moody's Portable Desktop App
echo ============================================
echo.

:: Check requirements
where php >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo [ERROR] PHP not found! Make sure PHP is in your PATH.
    pause
    exit /b 1
)

where composer >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Composer not found!
    pause
    exit /b 1
)

:: Clean and create build directory
set BUILD_DIR=%~dp0build\MoodysApp
if exist "%BUILD_DIR%" rmdir /s /q "%BUILD_DIR%"
mkdir "%BUILD_DIR%"
mkdir "%BUILD_DIR%\app"
mkdir "%BUILD_DIR%\database"

echo [1/5] Copying application files...
robocopy "%~dp0" "%BUILD_DIR%\app" /E /XD vendor node_modules .git storage\logs build desktop /XF .env .env.example >nul

echo [2/5] Making migrations SQLite-compatible...
cd /d "%BUILD_DIR%\app"

:: Replace enum() with string() in all migrations (SQLite-compatible)
powershell -Command "
$files = Get-ChildItem -Path 'database\migrations' -Filter '*.php'
foreach ($f in $files) {
    $content = Get-Content $f.FullName -Raw
    $content = $content -replace \"->enum\(['\""]([^'\""]*)['\""],\s*\[([^\]]*)\]\)\", \"->string('\$1')\"
    $content = $content -replace \"->after\(['\""][^'\""]*['\""]\)\"
    Set-Content $f.FullName $content
}
"

echo [3/5] Installing production dependencies...
call composer install --no-dev --optimize-autoloader --quiet

echo [4/5] Configuring SQLite database...
:: Create .env for desktop
copy .env.example .env >nul
php -r "
    \$env = file_get_contents('.env');
    \$env = preg_replace('/APP_ENV=.*/', 'APP_ENV=production', \$env);
    \$env = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=false', \$env);
    \$env = preg_replace('/APP_URL=.*/', 'APP_URL=http://localhost:8080', \$env);
    \$env = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=sqlite', \$env);
    \$env = preg_replace('/DB_HOST=.*/', '', \$env);
    \$env = preg_replace('/DB_PORT=.*/', '', \$env);
    \$env = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE='.__DIR__.'\\\\database\\\\database.sqlite', \$env);
    \$env = preg_replace('/DB_USERNAME=.*/', '', \$env);
    \$env = preg_replace('/DB_PASSWORD=.*/', '', \$env);
    \$env = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=file', \$env);
    \$env = preg_replace('/CACHE_STORE=.*/', 'CACHE_STORE=file', \$env);
    \$env = preg_replace('/QUEUE_CONNECTION=.*/', 'QUEUE_CONNECTION=sync', \$env);
    file_put_contents('.env', \$env);
    echo 'Created .env' . PHP_EOL;
"

:: Cache config (but SQLite path won't work in cache, so skip config:cache)
php artisan key:generate --quiet
php artisan storage:link --quiet

:: Create empty SQLite file and run migrations
copy NUL "database\database.sqlite" >nul
php artisan migrate --force --quiet

:: Seed essential data
php artisan db:seed --class=TenantSeeder --force --quiet
php artisan db:seed --class=UserSeeder --force --quiet
php artisan db:seed --class=ExpenseCategorySeeder --force --quiet
php artisan db:seed --class=ProductCategorySeeder --force --quiet
php artisan db:seed --class=ProductSeeder --force --quiet
php artisan db:seed --class=SupplierSeeder --force --quiet

echo [5/5] Copying launcher...
copy "%~dp0desktop\start.bat" "%BUILD_DIR%\" >nul
copy "%~dp0desktop\stop.bat" "%BUILD_DIR%\" >nul

:: Copy PHP from XAMPP
echo.
echo [Optional] Copying PHP from XAMPP...
if exist "C:\xampp\php" (
    mkdir "%BUILD_DIR%\php" 2>nul
    xcopy /E /I /Y "C:\xampp\php" "%BUILD_DIR%\php" >nul
    echo   PHP copied from XAMPP
) else (
    echo   WARNING: PHP not bundled. User must install PHP 8.2+ separately.
)

echo.
echo ============================================
echo   Package built successfully!
echo ============================================
echo.
echo Location: %BUILD_DIR%
echo.
echo To distribute:
echo   Zip the "MoodysApp" folder and share it.
echo.
echo To use on another laptop:
echo   1. Install PHP 8.2+ or bundle portable PHP in php\ folder
echo   2. Run start.bat
echo   3. Open http://localhost:8080
echo.
echo Login: admin@althwq.com / password
echo.
pause
