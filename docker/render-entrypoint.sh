#!/bin/sh
set -eu

# Render generates a random base64-encoded 256-bit value for APP_KEY.
# Laravel expects that value to carry the "base64:" prefix.
case "${APP_KEY:-}" in
    base64:*) ;;
    "") echo "APP_KEY must be set in the Render environment." >&2; exit 1 ;;
    *) export APP_KEY="base64:${APP_KEY}" ;;
esac

mkdir -p storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

php artisan migrate --force
php artisan db:seed --force

exec apache2-foreground
