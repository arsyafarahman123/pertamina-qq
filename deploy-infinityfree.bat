@echo off
title Deploy ke InfinityFree — Pertamina QQ FT Maos
color 0B

echo ================================================================
echo   DEPLOY PERTAMINA QQ KE INFINITYFREE
echo   pertamina-qq-maos.infinityfreeapp.com
echo ================================================================
echo.

echo ================================================================
echo   LANGKAH-LANGKAH DEPLOY (Manual via FTP):
echo ================================================================
echo.
echo   1. BUAT DATABASE MYSQL di panel InfinityFree:
echo      - Login ke https://dash.infinityfree.com
echo      - Masuk ke akun hosting, klik "MySQL Databases"
echo      - Buat database baru, catat:
echo        * DB Host (sql3XX.infinityfree.com)
echo        * DB Name (if0_XXXXX_namadb)
echo        * DB Username (if0_XXXXX)
echo        * DB Password (yang kamu set)
echo.
echo   2. EDIT FILE .env.production:
echo      - Ganti DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
echo        dengan detail MySQL dari langkah 1
echo      - File ada di: %~dp0.env.production
echo.
echo   3. UPLOAD SEMUA FILE via FTP (FileZilla):
echo      - Host: ftpupload.net
echo      - Username/Password: dari panel InfinityFree
echo      - Upload SEMUA isi folder ini ke folder htdocs/
echo      - PENTING: Rename .env.production jadi .env di server
echo      - Pastikan folder vendor/ ikut ter-upload
echo.
echo   4. JALANKAN SETUP via Browser:
echo      - Buka: https://pertamina-qq-maos.infinityfreeapp.com/setup.php
echo      - Tunggu sampai semua langkah selesai (hijau semua)
echo      - HAPUS file setup.php setelah selesai!
echo.
echo   5. TEST LOGIN:
echo      - Buka: https://pertamina-qq-maos.infinityfreeapp.com/login
echo      - Login: admin@labqq.test / password123
echo.
echo ================================================================
echo   CATATAN PENTING:
echo ================================================================
echo.
echo   - InfinityFree GRATIS dan ONLINE 24/7 (laptop boleh mati)
echo   - Tidak perlu jalankan server lokal lagi
echo   - Data tersimpan PERMANEN di MySQL InfinityFree
echo   - Max file PHP/HTML = 1MB, file lain = 10MB
echo   - Jangan upload file .env (rename .env.production jadi .env di server)
echo.
echo ================================================================
pause
