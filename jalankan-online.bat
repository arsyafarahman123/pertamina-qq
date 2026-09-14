@echo off
title Server Online Pertamina QQ FT Maos (Cloudflare Tunnel)
color 0B

echo ================================================================
echo    SERVER ONLINE PERTAMINA QQ - FUEL TERMINAL MAOS
echo ================================================================
echo.
echo Sedang menjalankan PHP Server & Cloudflare Tunnel...
echo.

start /B php artisan serve --host=127.0.0.1 --port=8000
timeout /t 2 >nul

echo ================================================================
echo  SERVER AKTIF! Link online Anda akan muncul di bawah ini:
echo ================================================================
echo.

npx.cmd -y cloudflared tunnel --url http://localhost:8000
pause
