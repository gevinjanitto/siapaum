#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

composer install --no-interaction --prefer-dist

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ -f .env ] && grep -q '^APP_KEY=$' .env; then
    php artisan key:generate --force
fi

php artisan storage:link >/dev/null 2>&1 || true

exec "$@"
