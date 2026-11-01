#!/bin/bash
set -e

echo "🚀 Memulai Deployment..."

cd "$(dirname "$0")"
# 1. Git Pull
# Pastikan folder .git juga bisa dibaca oleh www-data atau gunakan git config global safe directory
git pull origin

# 2. Migrasi
php artisan migrate --force

# 3. Optimize (Membersihkan dan membuat ulang cache)
# File cache baru otomatis akan milik group www-data berkat setup sticky bit tadi
php artisan optimize

echo "✅ Deployment Selesai!"
