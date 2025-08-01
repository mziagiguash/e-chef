# Stage 1: Composer
FROM composer:2 as composer

# Stage 2: PHP + Laravel
FROM php:8.4-fpm

# Установка системных пакетов и PHP-расширений
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo_mysql

# Установка Composer из первого stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

# Копируем проект
COPY . .

# Права на важные директории
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
