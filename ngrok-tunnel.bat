@echo off
REM Build asset pakai Vite ke public/build
echo Membangun asset...
call npm run build

REM Jalankan Laravel di window terpisah
start "Laravel Server" cmd /k "php artisan serve"

REM Tunggu 2 detik biar Laravel siap
timeout /t 2 >nul

REM Jalankan ngrok untuk Laravel
ngrok http --url=hagiological-marchelle-waspier.ngrok-free.app 8000

pause
