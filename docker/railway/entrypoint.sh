#!/bin/sh
set -e
cd /var/www/html

PORT="${PORT:-8080}"
# Apache + mod_php hanya boleh 1 MPM (prefork); cegah "More than one MPM loaded"
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
[ -e /etc/apache2/mods-enabled/mpm_prefork.load ] || a2enmod mpm_prefork >/dev/null
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

case "${APP_KEY:-}" in
    "") export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"; echo "!! APP_KEY belum diisi di Railway Variables, dibuat sementara (sesi login reset tiap deploy)" ;;
    base64:*) ;;
    *) export APP_KEY="base64:${APP_KEY}" ;;
esac
[ -z "${APP_URL:-}" ] && [ -n "${RAILWAY_PUBLIC_DOMAIN:-}" ] && export APP_URL="https://${RAILWAY_PUBLIC_DOMAIN}"

# Pakai variabel bawaan plugin MySQL Railway jika DB_* tidak diisi
export DB_CONNECTION="${DB_CONNECTION:-mysql}"
[ -z "${DB_HOST:-}" ] && [ -n "${MYSQLHOST:-}" ] && export DB_HOST="$MYSQLHOST"
[ -z "${DB_PORT:-}" ] && [ -n "${MYSQLPORT:-}" ] && export DB_PORT="$MYSQLPORT"
[ -z "${DB_DATABASE:-}" ] && [ -n "${MYSQLDATABASE:-}" ] && export DB_DATABASE="$MYSQLDATABASE"
[ -z "${DB_USERNAME:-}" ] && [ -n "${MYSQLUSER:-}" ] && export DB_USERNAME="$MYSQLUSER"
[ -z "${DB_PASSWORD:-}" ] && [ -n "${MYSQLPASSWORD:-}" ] && export DB_PASSWORD="$MYSQLPASSWORD"

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan storage:link >/dev/null 2>&1 || true

php artisan config:clear >/dev/null 2>&1 || true
echo "==> Menunggu database MySQL & menjalankan migrasi"
i=0
until php artisan migrate --force; do
    i=$((i + 1))
    [ "$i" -ge 12 ] && { echo "!! Migrasi gagal setelah 12 percobaan, cek variabel DB_* di Railway"; break; }
    sleep 5
done

USERS="$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null | tail -n 1 || echo 1)"
if [ "$USERS" = "0" ] || [ "${RUN_SEED:-false}" = "true" ]; then
    echo "==> Database kosong, menjalankan seeder awal"
    php artisan db:seed --force || echo "!! Seeder gagal"
fi

php artisan config:cache
php artisan view:cache || true
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
