@echo off
cd /d "%~dp0"
if exist public\hot del public\hot
echo ==^> SIAP AUM berjalan di http://localhost:8000  (Ctrl+C untuk berhenti)
php artisan serve --host=127.0.0.1 --port=8000
