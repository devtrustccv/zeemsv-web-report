FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package*.json vite.config.js ./
COPY resources ./resources

RUN npm ci && npm run build


FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize && php artisan package:discover --ansi


FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libonig-dev \
    libpng-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install bcmath exif gd mbstring pcntl pdo_mysql pdo_pgsql zip \
    && a2enmod rewrite headers \
    && sed -ri -e "s!/var/www/html!/var/www/html/public!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!/var/www/html/public!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app ./
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p \
    storage/app \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 80
