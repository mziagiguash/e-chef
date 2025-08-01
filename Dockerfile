# Stage 1: Composer dependencies
FROM composer:2 as composer

# Stage 2: Laravel App with PHP
FROM php:8.2-cli

# Установим системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# Установка Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

# Копируем проект
COPY . .

# Установка зависимостей
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Генерация application key
RUN php artisan config:clear && php artisan key:generate

# Права
RUN chmod -R 775 storage bootstrap/cache

# Открываем порт
EXPOSE 8080

# Запуск Laravel через встроенный сервер
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
