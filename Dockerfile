FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
    curl \
    curl-dev \
    libcurl \
    libssl3 \
    libcrypto3 && \
    docker-php-ext-install curl session pdo pdo_mysql


FROM base AS builder

RUN apk add --no-cache git unzip

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev --prefer-dist --no-scripts --no-progress --optimize-autoloader

FROM base AS runtime

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

COPY docker/php-app-entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
