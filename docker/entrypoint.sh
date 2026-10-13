#!/bin/sh
set -e
cd /var/www/html

[ -f .env ] || cp .env.example .env
# Use APP_KEY from the environment (.env via compose) if provided
if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=base64' .env; then
    php artisan key:generate --force
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-database/database.sqlite}"
    mkdir -p "$(dirname "$DB_FILE")"
    [ -f "$DB_FILE" ] || touch "$DB_FILE"
    chown -R www-data:www-data "$(dirname "$DB_FILE")"
fi
chown -R www-data:www-data storage bootstrap/cache

php artisan package:discover --ansi
php artisan migrate --force
# Seeder is idempotent (updateOrCreate / firstOrCreate), safe on every start
php artisan db:seed --force
php artisan config:cache
php artisan view:cache

exec "$@"
