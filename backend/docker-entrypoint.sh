#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate --no-interaction --force
php artisan config:clear
php artisan migrate --force --seed

exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=80 --admin-port=2019
