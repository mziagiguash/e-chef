# Stage 1: Composer
FROM composer:2 as composer

# Stage 2: PHP + Laravel
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo_mysql

# Установка Composer из первого stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Установка зависимостей
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Права
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Открываем порт
EXPOSE 8080

# CMD теперь не вызывает artisan
CMD ["php-fpm"]
