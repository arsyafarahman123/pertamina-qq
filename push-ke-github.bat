@echo off
title Auto Push ke GitHub - Pertamina QQ FT Maos
color 0A

echo ========================================================
echo    AUTO PUSH KE GITHUB: pertamina-qq
echo    https://github.com/arsyafarahman123/pertamina-qq
echo ========================================================
echo.

echo [1/4] Memeriksa status file git...
git status -s

echo.
echo [2/4] Menambahkan semua perubahan (git add .)...
git add .

echo.
echo [3/4] Melakukan commit perubahan...
set /p commit_msg="Masukkan pesan commit (tekan Enter untuk default): "
if "%commit_msg%"=="" set commit_msg=Update aplikasi Pertamina QQ - %date% %time%

git commit -m "%commit_msg%"

echo.
echo [4/4] Mengupload ke GitHub (git push origin main)...
git branch -M main
git push -u origin main

echo.
if %errorlevel% equ 0 (
    echo ========================================================
    echo  BERHASIL! Kode aplikasi sudah ter-upload ke GitHub!
    echo ========================================================
) else (
    echo ========================================================
    echo  GAGAL ATAU BUTUH LOGIN. Silakan cek pesan di atas.
    echo ========================================================
)
echo.
pause
