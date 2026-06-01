@echo off
title Moody's - Stop Server
echo Stopping Moody's Restaurant...
taskkill /f /im php.exe >nul 2>&1
echo Done.
timeout /t 2 /nobreak >nul
