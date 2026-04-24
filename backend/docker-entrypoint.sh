#!/bin/sh
set -e

php artisan config:clear
php artisan migrate --force --seed

exec php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=80 --admin-port=2019
