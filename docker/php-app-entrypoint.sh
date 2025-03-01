#!/bin/sh
set -e

if [ ! -f "/var/www/html/.env" ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

php artisan migrate --force

DB_HOST=db-testing php artisan test

exec php-fpm