param(
    [string]$OutputDir = "$PSScriptRoot\build\MoodysApp"
)

$ErrorActionPreference = 'Continue'

Write-Host "============================================" -ForegroundColor Yellow
Write-Host "  Building Moody's Management Desktop App" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""

# --- Requirements ---
$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) { Write-Host "[ERROR] PHP not found in PATH!" -ForegroundColor Red; exit 1 }
$composer = Get-Command composer -ErrorAction SilentlyContinue
if (-not $composer) { Write-Host "[ERROR] Composer not found!" -ForegroundColor Red; exit 1 }
$srcPhp = "C:\xampp\php"
if (-not (Test-Path "$srcPhp\php.exe")) { Write-Host "[ERROR] XAMPP PHP not found at $srcPhp" -ForegroundColor Red; exit 1 }

# --- Clean output ---
if (Test-Path $OutputDir) {
    try { Remove-Item -Path $OutputDir -Recurse -Force -ErrorAction Stop }
    catch {
        Write-Host "[WARN] Could not fully clean $OutputDir (locked file). Removing what's possible..." -ForegroundColor Yellow
        Get-ChildItem $OutputDir -Force -ErrorAction SilentlyContinue | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
    }
}
New-Item -ItemType Directory -Path "$OutputDir\app" -Force | Out-Null

Write-Host "[1/6] Copying application files..." -ForegroundColor Cyan
$exclude = @("$PSScriptRoot\vendor", 'node_modules', '.git', 'storage\logs', 'build', 'desktop', 'database\database.sqlite')
robocopy $PSScriptRoot "$OutputDir\app" /E /XD $exclude /XF '.env' /NFL /NDL /NJH /NJS /NP | Out-Null

# Ensure writable storage dirs exist
foreach ($d in @("$OutputDir\app\storage\framework\sessions", "$OutputDir\app\storage\framework\cache\data", "$OutputDir\app\storage\framework\views", "$OutputDir\app\storage\logs", "$OutputDir\app\bootstrap\cache")) {
    if (-not (Test-Path $d)) { New-Item -ItemType Directory -Path $d -Force | Out-Null }
}

Write-Host "[2/6] Copying PHP runtime (portable)..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path "$OutputDir\php" -Force | Out-Null
# Copy only the essential PHP files (no XAMPP bloat folders, no nested prior builds)
$phpTopExclude = @('windowsXamppPhp', 'tests', 'docs', 'man', 'www', 'scripts', 'CompatInfo', 'dev', 'cfg', 'data', 'logs', 'tmp', 'pear', 'pci')
Get-ChildItem $srcPhp | Where-Object { $_.Name -notin $phpTopExclude } | Copy-Item -Destination "$OutputDir\php\" -Recurse -Force
# Keep the folders the launcher expects (listed as mounts at runtime)
New-Item -ItemType Directory -Path "$OutputDir\php\tmp" -Force | Out-Null
New-Item -ItemType Directory -Path "$OutputDir\php\logs" -Force | Out-Null
$phpSize = [math]::Round((Get-ChildItem "$OutputDir\php" -Recurse -ErrorAction SilentlyContinue | Measure-Object Length -Sum).Sum / 1MB, 1)
Write-Host "      PHP copied ($phpSize MB)" -ForegroundColor Green

Write-Host "[3/6] Fixing php.ini for portability..." -ForegroundColor Cyan
$phpIni = "$OutputDir\php\php.ini"
if (Test-Path $phpIni) {
    $ini = Get-Content $phpIni -Raw

    # CRITICAL: disable browscap (causes "Unable to start standard module" crash in ZTS builds)
    $ini = $ini -replace '(?m)^\s*browscap\s*=.*', 'browscap=""'

    # Enable sqlite3 extension (pdo_sqlite is already enabled)
    $ini = $ini -replace '(?m)^;?\s*extension\s*=\s*sqlite3.*', 'extension=sqlite3'

    # Make all paths relative so it works from ANY install location (USB, external drive, etc.)
    $ini = $ini -replace [regex]::Escape('C:\xampp\php\ext'), './ext'
    $ini = $ini -replace [regex]::Escape('C:\xampp\php\PEAR'), 'PEAR'
    $ini = $ini -replace [regex]::Escape('C:\xampp\tmp'), './tmp'
    $ini = $ini -replace [regex]::Escape('C:\xampp\php\logs'), './logs'
    $ini = $ini -replace 'upload_tmp_dir\s*=.*', 'upload_tmp_dir="./tmp"'
    $ini = $ini -replace 'session\.save_path\s*=.*', 'session.save_path="./tmp"'
    $ini = $ini -replace 'error_log\s*=.*', 'error_log="./logs/php_error_log"'
    $ini = $ini -replace [regex]::Escape('C:\xampp\apache\bin\curl-ca-bundle.crt'), ''
    $ini = $ini -replace 'curl\.cainfo\s*=.*', '; curl.cainfo='
    $ini = $ini -replace 'openssl\.cafile\s*=.*', '; openssl.cafile='

    Set-Content $phpIni $ini
    Write-Host "      browscap disabled, sqlite3 enabled, paths made portable"
}

Write-Host "[4/6] Installing production dependencies..." -ForegroundColor Cyan
Push-Location "$OutputDir\app"
try {
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --quiet 2>&1
} finally { Pop-Location }

Write-Host "[5/6] Configuring SQLite database + seed data..." -ForegroundColor Cyan
Push-Location "$OutputDir\app"
try {
    Copy-Item ".env.example" ".env" -Force

    $envContent = Get-Content ".env" -Raw
    $envContent = $envContent -replace 'APP_NAME=Laravel', 'APP_NAME="Moody''s Management"'
    $envContent = $envContent -replace 'APP_ENV=.*', 'APP_ENV=production'
    $envContent = $envContent -replace 'APP_DEBUG=.*', 'APP_DEBUG=false'
    $envContent = $envContent -replace 'APP_URL=.*', 'APP_URL=http://127.0.0.1:8080'
    $envContent = $envContent -replace 'APP_LOCALE=.*', 'APP_LOCALE=ar'
    $envContent = $envContent -replace 'APP_FAKER_LOCALE=.*', 'APP_FAKER_LOCALE=ar_SA'
    $envContent = $envContent -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=sqlite'
    $envContent = $envContent -replace 'SESSION_DRIVER=.*', 'SESSION_DRIVER=file'
    $envContent = $envContent -replace 'CACHE_STORE=.*', 'CACHE_STORE=file'
    $envContent = $envContent -replace 'QUEUE_CONNECTION=.*', 'QUEUE_CONNECTION=sync'
    $envContent = $envContent -replace '# DB_HOST=.*', ''
    $envContent = $envContent -replace '# DB_PORT=.*', ''
    $envContent = $envContent -replace '# DB_DATABASE=.*', ''
    $envContent = $envContent -replace '# DB_USERNAME=.*', ''
    $envContent = $envContent -replace '# DB_PASSWORD=.*', ''
    Set-Content ".env" $envContent

    New-Item -ItemType File -Path "database\database.sqlite" -Force | Out-Null

    php artisan key:generate --force --quiet
    php artisan storage:link --quiet 2>$null
    php artisan migrate --force --quiet
    php artisan db:seed --force --quiet
    php artisan optimize:clear --quiet
    Write-Host "      Database migrated and seeded" -ForegroundColor Green
} finally { Pop-Location }

Write-Host "[6/6] Creating launchers..." -ForegroundColor Cyan
# --- start.bat ---
@"
@echo off
title Moody's Management
setlocal enabledelayedexpansion
set PORT=8080
set MAX_PORT=8100
set APP_DIR=%~dp0

if not exist "%APP_DIR%php\php.exe" (
    echo ERROR: PHP runtime not found. Reinstall the app.
    pause
    exit /b 1
)
if not exist "%APP_DIR%app\artisan" (
    echo ERROR: Application files not found.
    pause
    exit /b 1
)

:check_port
netstat -ano 2>nul | findstr /R /C:":%PORT% .*LISTENING" >nul 2>&1
if !errorlevel! equ 0 (
    set /a PORT+=1
    if !PORT! gtr !MAX_PORT! (
        echo ERROR: No available port in range 8080-8100.
        pause
        exit /b 1
    )
    goto check_port
)

REM Ensure storage symlink exists (works without admin rights)
if not exist "%APP_DIR%app\public\storage" (
    mklink /J "%APP_DIR%app\public\storage" "%APP_DIR%app\storage\app\public" >nul 2>&1
)
if not exist "%APP_DIR%php\tmp" mkdir "%APP_DIR%php\tmp" >nul 2>&1
if not exist "%APP_DIR%php\logs" mkdir "%APP_DIR%php\logs" >nul 2>&1

echo.
echo  ==========================================
echo    Moody's Management - Local Server
echo  ==========================================
echo    Starting on port !PORT!...
echo    Close this window to stop the app.
echo  ==========================================
echo.

REM Open the browser after a short delay
start "" /b cmd /c "timeout /t 3 /nobreak >nul & start "" http://127.0.0.1:!PORT! & exit"

cd /d "%APP_DIR%app"
"%APP_DIR%php\php.exe" -c "%APP_DIR%php\php.ini" artisan serve --host=127.0.0.1 --port=!PORT!

echo.
echo Server stopped.
timeout /t 2 /nobreak >nul
"@ | Out-File -FilePath "$OutputDir\start.bat" -Encoding ASCII

# --- stop.bat ---
@"
@echo off
title Moody's - Stop
echo Stopping Moody's Management...
taskkill /f /im php.exe >nul 2>&1
echo Done. You can close this window.
timeout /t 1 /nobreak >nul
"@ | Out-File -FilePath "$OutputDir\stop.bat" -Encoding ASCII

# --- favicon / installer icon ---
Write-Host "[+] Using premium app icon..." -ForegroundColor Cyan
$genIco = "$PSScriptRoot\generated\app-icon.ico"
if (Test-Path $genIco) {
    Copy-Item $genIco "$OutputDir\app-icon.ico" -Force
    Write-Host "      app-icon.ico copied from generated assets" -ForegroundColor Green
} else {
    try {
        Add-Type -AssemblyName System.Drawing -ErrorAction Stop
        $faviconPath = "$OutputDir\app-icon.ico"
        $bmp = New-Object System.Drawing.Bitmap 32,32
        $g = [System.Drawing.Graphics]::FromImage($bmp)
        $g.Clear([System.Drawing.Color]::FromArgb(26, 26, 46))
        $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(212, 168, 83))
        $g.FillEllipse($brush, 6, 6, 20, 20)
        $font = New-Object System.Drawing.Font("Tahoma", 13, [System.Drawing.FontStyle]::Bold)
        $textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(26, 26, 46))
        $g.DrawString("M", $font, $textBrush, 8, 4)
        $g.Dispose(); $brush.Dispose(); $font.Dispose(); $textBrush.Dispose()
        $hIcon = $bmp.GetHicon()
        $icon = [System.Drawing.Icon]::FromHandle($hIcon)
        $fs = New-Object System.IO.FileStream($faviconPath, [System.IO.FileMode]::Create)
        $icon.Save($fs); $fs.Close()
        $icon.Dispose(); $bmp.Dispose()
        Write-Host "      fallback icon generated" -ForegroundColor Yellow
    } catch {
        Write-Host "      icon skipped ($_)" -ForegroundColor Yellow
    }
}

Write-Host ""
$total = [math]::Round((Get-ChildItem $OutputDir -Recurse -ErrorAction SilentlyContinue | Measure-Object Length -Sum).Sum / 1MB, 1)
Write-Host "============================================" -ForegroundColor Yellow
Write-Host "  Package built successfully!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Yellow
Write-Host ""
Write-Host "  Location : $OutputDir" -ForegroundColor White
Write-Host "  Size     : $total MB" -ForegroundColor White
Write-Host ""
Write-Host "  To run   : double-click start.bat" -ForegroundColor Cyan
Write-Host "  Login    : admin@althwq.com / password" -ForegroundColor Yellow
Write-Host ""