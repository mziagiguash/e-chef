# Stage 1: Composer
FROM composer:2 AS composer

# Stage 2: PHP + Laravel
FROM php:8.4-fpm

# Установка системных зависимостей и PHP расширений
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd

# Копируем composer из первого stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем все файлы проекта в контейнер
COPY . .

# Установка PHP-зависимостей проекта
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Правильные права на storage и кеш
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Открываем порт 9000 — стандарт для php-fpm
EXPOSE 9000

# Запуск php-fpm (php:8.4-fpm запускает php-fpm по умолчанию)
CMD ["php-fpm"]
