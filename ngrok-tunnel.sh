#!/bin/bash

# Build asset pakai Vite ke public/build
echo "Membangun asset..."
npm run build

# Jalankan Laravel di background
php artisan serve

# Tunggu 2 detik biar Laravel siap
sleep 2

# Jalankan ngrok untuk Laravel
# ./ngrok http 8000

./ngrok http --url=hagiological-marchelle-waspier.ngrok-free.app 8000
