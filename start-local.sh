#!/usr/bin/env bash
# Menjalankan SIAP AUM secara lokal: http://localhost:8000
cd "$(dirname "$0")"
PORT="${PORT:-8000}"
if [ "${1:-}" = "dev" ]; then
  echo "==> Mode dev: Vite (hot reload) + Laravel"
  (npm run dev >/tmp/siap-aum-vite.log 2>&1 &)
else
  rm -f public/hot
fi
echo "==> SIAP AUM berjalan di http://localhost:$PORT  (Ctrl+C untuk berhenti)"
php artisan serve --host=127.0.0.1 --port="$PORT"
