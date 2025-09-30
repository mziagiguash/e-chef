FROM php:8.4-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Копируем скрипт запуска
COPY migrate-and-serve.sh /migrate-and-serve.sh
RUN chmod +x /migrate-and-serve.sh

EXPOSE 8000

CMD ["/migrate-and-serve.sh"]
