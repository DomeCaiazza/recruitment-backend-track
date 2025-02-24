#!/bin/sh
set -e

if [ ! -f "/var/www/html/.env" ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

php artisan migrate --force

exec php-fpm