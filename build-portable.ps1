param(
    [string]$OutputDir = "$PSScriptRoot\build\MoodysApp"
)

Write-Host "============================================" -ForegroundColor Yellow
Write-Host "  Building Moody's Management Desktop App" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""

# Check requirements
$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) {
    Write-Host "[ERROR] PHP not found! Make sure PHP is in your PATH." -ForegroundColor Red
    exit 1
}

$composer = Get-Command composer -ErrorAction SilentlyContinue
if (-not $composer) {
    Write-Host "[ERROR] Composer not found!" -ForegroundColor Red
    exit 1
}

# Clean and create build directory
if (Test-Path $OutputDir) { Remove-Item -Path $OutputDir -Recurse -Force }
New-Item -ItemType Directory -Path "$OutputDir\app" -Force | Out-Null

Write-Host "[1/5] Copying application files..." -ForegroundColor Cyan
$exclude = @('vendor', 'node_modules', '.git', 'storage\logs', 'build', 'desktop')
robocopy $PSScriptRoot "$OutputDir\app" /E /XD $exclude /XF '.env' /NFL /NDL /NJH /NJS | Out-Null

Write-Host "[2/5] Making migrations SQLite-compatible..." -ForegroundColor Cyan
$migrations = Get-ChildItem "$OutputDir\app\database\migrations" -Filter "*.php"
foreach ($f in $migrations) {
    $content = Get-Content $f.FullName -Raw
    # Replace enum('col', [val1, val2]) with string('col')
    $content = $content -replace "->enum\(`"([^`"]+)`",\s*\[([^\]]*)\]\)", "->string(`"`$1`")"
    $content = $content -replace "->enum\('([^']+)',\s*\[([^\]]*)\]\)", "->string('`$1')"
    # Remove ->after() calls
    $content = $content -replace "->after\([^)]+\)\s*", ""
    Set-Content $f.FullName $content
}
Write-Host "      Modified $($migrations.Count) migration files" -ForegroundColor Green

Write-Host "[3/5] Installing production dependencies..." -ForegroundColor Cyan
Push-Location "$OutputDir\app"
try {
    composer install --no-dev --optimize-autoloader --quiet 2>&1 | Out-Null
    Write-Host "      Composer dependencies installed" -ForegroundColor Green
} finally {
    Pop-Location
}

Write-Host "[4/5] Configuring SQLite database..." -ForegroundColor Cyan
Push-Location "$OutputDir\app"
try {
    # Create .env
    Copy-Item ".env.example" ".env" -Force

    # Update .env for desktop/SQLite mode
    $envContent = Get-Content ".env" -Raw
    $replacements = @{
        'APP_ENV=.*' = 'APP_ENV=production'
        'APP_DEBUG=.*' = 'APP_DEBUG=false'
        'APP_URL=.*' = 'APP_URL=http://localhost:8080'
        'DB_CONNECTION=.*' = 'DB_CONNECTION=sqlite'
        'SESSION_DRIVER=.*' = 'SESSION_DRIVER=file'
        'CACHE_STORE=.*' = 'CACHE_STORE=file'
        'QUEUE_CONNECTION=.*' = 'QUEUE_CONNECTION=sync'
    }
    foreach ($pattern in $replacements.Keys) {
        $envContent = $envContent -replace $pattern, $replacements[$pattern]
    }
    # Remove MySQL-specific lines
    $envContent = $envContent -replace "DB_HOST=[# ]?.*`r?`n", ""
    $envContent = $envContent -replace "DB_PORT=[# ]?.*`r?`n", ""
    $envContent = $envContent -replace "# DB_DATABASE=.*`r?`n", "DB_DATABASE=$OutputDir\app\database\database.sqlite`r`n"
    $envContent = $envContent -replace "DB_USERNAME=[# ]?.*`r?`n", ""
    $envContent = $envContent -replace "DB_PASSWORD=[# ]?.*`r?`n", ""
    Set-Content ".env" $envContent

    # Generate key and storage link
    php artisan key:generate --quiet
    php artisan storage:link --quiet

    # Create SQLite database and run migrations
    New-Item -ItemType File -Path "$OutputDir\app\database\database.sqlite" -Force | Out-Null
    php artisan migrate --force --quiet
    Write-Host "      Database migrated" -ForegroundColor Green

    # Seed data
    php artisan db:seed --class=TenantSeeder --force --quiet
    php artisan db:seed --class=UserSeeder --force --quiet
    php artisan db:seed --class=ExpenseCategorySeeder --force --quiet
    php artisan db:seed --class=ProductCategorySeeder --force --quiet
    php artisan db:seed --class=ProductSeeder --force --quiet
    php artisan db:seed --class=SupplierSeeder --force --quiet
    Write-Host "      Database seeded with sample data" -ForegroundColor Green

    # Clear config/route/view cache for SQLite path to work
    php artisan optimize:clear --quiet

} finally {
    Pop-Location
}

Write-Host "[5/5] Creating launcher..." -ForegroundColor Cyan
# Create start.bat
@"
@echo off
title Moody's Management
setlocal enabledelayedexpansion
set PORT=8080
set MAX_PORT=8100
set APP_DIR=%~dp0
cd /d "%APP_DIR%"

:check_port
netstat -ano 2>nul | findstr ":%PORT% " >nul 2>&1
if !errorlevel! equ 0 (
    set /a PORT+=1
    if !PORT! gtr !MAX_PORT! (
        echo ERROR: No available port (8080-8100).
        pause
        exit /b 1
    )
    goto check_port
)

if not exist "php\php.exe" (
    where php >nul 2>&1
    if errorlevel 1 (
        echo ERROR: PHP not found!
        pause
        exit /b 1
    )
    set PHP_PATH=php
) else (
    set PHP_PATH=%APP_DIR%php\php.exe
)

if not exist "%APP_DIR%app\public" (
    echo ERROR: Application files not found!
    pause
    exit /b 1
)

echo Starting server on port !PORT!...
start /B "" "!PHP_PATH!" -S 127.0.0.1:!PORT! -t "%APP_DIR%app\public" > "%APP_DIR%php_server.log" 2>&1

echo Waiting for server...
timeout /t 4 /nobreak >nul

:open_app
where msedge >nul 2>&1
if not errorlevel 1 (
    start "" msedge --app=http://127.0.0.1:!PORT! --no-first-run
    goto wait
)
where chrome >nul 2>&1
if not errorlevel 1 (
    start "" chrome --app=http://127.0.0.1:!PORT! --no-first-run
    goto wait
)
where firefox >nul 2>&1
if not errorlevel 1 (
    start "" firefox -new-window http://127.0.0.1:!PORT!
    goto wait
)
start http://127.0.0.1:!PORT!

:wait
echo.
echo App running at http://127.0.0.1:!PORT!
echo Close this window to stop the server.
echo.
pause
taskkill /f /im php.exe >nul 2>&1
"@ | Out-File -FilePath "$OutputDir\start.bat" -Encoding ASCII

# Create stop.bat
@"
@echo off
title Moody's - Stop Server
echo Stopping Moody's Management...
taskkill /f /im php.exe >nul 2>&1
echo Done.
timeout /t 2 /nobreak >nul
"@ | Out-File -FilePath "$OutputDir\stop.bat" -Encoding ASCII

# Copy PHP from XAMPP if available
Write-Host "[Optional] Copying PHP from XAMPP..." -ForegroundColor Cyan
$xamppPhp = "C:\xampp\php"
if (Test-Path $xamppPhp) {
    New-Item -ItemType Directory -Path "$OutputDir\php" -Force | Out-Null
    Copy-Item "$xamppPhp\*" "$OutputDir\php\" -Recurse -Force
    $phpSize = [math]::Round((Get-ChildItem "$OutputDir\php" -Recurse | Measure-Object Length -Sum).Sum / 1MB, 1)

    # Fix php.ini: replace hardcoded XAMPP paths with portable relative paths
    $phpIni = "$OutputDir\php\php.ini"
    if (Test-Path $phpIni) {
        $ini = Get-Content $phpIni -Raw
        $ini = $ini -replace [regex]::Escape('C:\xampp\php\ext'), './ext'
        $ini = $ini -replace [regex]::Escape('C:\xampp\php\PEAR'), 'PEAR'
        # Use relative paths so it works from any install location
        $ini = $ini -replace [regex]::Escape('upload_tmp_dir="C:\xampp\tmp"'), 'upload_tmp_dir="storage\tmp"'
        $ini = $ini -replace [regex]::Escape('session.save_path="C:\xampp\tmp"'), 'session.save_path="storage\tmp"'
        $ini = $ini -replace [regex]::Escape('error_log="C:\xampp\php\logs\php_error_log"'), 'error_log="storage\logs\php_error_log"'
        $ini = $ini -replace [regex]::Escape('C:\xampp\apache\bin\curl-ca-bundle.crt'), ''
        $ini = $ini -replace 'curl\.cainfo=.*', '; curl.cainfo='
        $ini = $ini -replace 'openssl\.cafile=.*', '; openssl.cafile='
        Set-Content $phpIni $ini
        Write-Host "      php.ini paths fixed for portability" -ForegroundColor Green
    }

    Write-Host "      PHP copied from XAMPP ($phpSize MB)" -ForegroundColor Green
} else {
    Write-Host "      WARNING: PHP not bundled. User must install PHP 8.2+ separately." -ForegroundColor Yellow
}

# Generate a proper favicon.ico for the installer
Write-Host "[Optional] Generating favicon.ico..." -ForegroundColor Cyan
try {
    Add-Type -AssemblyName System.Drawing -ErrorAction Stop
    $faviconPath = "$OutputDir\favicon.ico"
    $bmp = New-Object System.Drawing.Bitmap 32,32
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.Clear([System.Drawing.Color]::FromArgb(26, 26, 46))
    $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(212, 168, 83))
    $g.FillEllipse($brush, 8, 8, 16, 16)
    $g.Dispose()
    $brush.Dispose()
    $hIcon = $bmp.GetHicon()
    $icon = [System.Drawing.Icon]::FromHandle($hIcon)
    $fs = New-Object System.IO.FileStream($faviconPath, [System.IO.FileMode]::Create)
    $icon.Save($fs)
    $fs.Close()
    $icon.Dispose()
    $bmp.Dispose()
    Write-Host "      favicon.ico generated" -ForegroundColor Green
} catch {
    # Fallback: copy the app's public favicon if generation fails
    Copy-Item "$OutputDir\app\public\favicon.ico" "$OutputDir\favicon.ico" -Force -ErrorAction SilentlyContinue
    Write-Host "      favicon.ico copied from public folder" -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Yellow
Write-Host "  Package built successfully!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""
Write-Host "  Location: $OutputDir" -ForegroundColor White
Write-Host "  Size: $([math]::Round((Get-ChildItem $OutputDir -Recurse | Measure-Object Length -Sum).Sum / 1MB, 1)) MB"
Write-Host ""
Write-Host "  To distribute: Zip the 'MoodysApp' folder" -ForegroundColor Cyan
Write-Host "  To use: Extract ^& run start.bat" -ForegroundColor Cyan
Write-Host "  Login:  admin@althwq.com / password" -ForegroundColor Yellow
Write-Host ""
