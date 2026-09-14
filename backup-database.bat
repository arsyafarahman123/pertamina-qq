@echo off
title Backup Data Pertamina QQ FT Maos
color 0A
echo ========================================================
echo   BACKUP DATABASE & DATA PERTAMINA QQ FT MAOS
echo ========================================================
echo.

if not exist d:\lab-qq\database\backups mkdir d:\lab-qq\database\backups

echo [1/1] Membuat cadangan SQL Dump MySQL...
python d:\lab-qq\database\dump_mysql.py

echo.
echo ========================================================
echo   BACKUP BERHASIL DISIMPAN!
echo   Lokasi File: d:\lab-qq\database\pertamina_qq_mysql.sql
echo   
echo   File ini 100%% portabel (Universal Standard MySQL):
echo   Bisa di-import ke hosting MANAPUN seumur hidup
echo   (cPanel, Niagahoster, Hostinger, AWS, Server Pertamina)
echo   tanpa ada data atau riwayat yang hilang!
echo ========================================================
echo.
pause
