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
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd

# Копируем composer из первого stage
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем все файлы проекта
COPY . .

# Правильные права на storage и кеш
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Открываем порт 9000
EXPOSE 9000

# Делаем entrypoint исполняемым
RUN chmod +x /entrypoint.sh

# Используем entrypoint для установки зависимостей
ENTRYPOINT ["/entrypoint.sh"]
