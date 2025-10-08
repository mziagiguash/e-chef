FROM php:8.2-fpm

# Установка системных утилит для отладки
RUN apt-get update && apt-get install -y \
    procps \
    net-tools \
    iputils-ping \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-install pdo pdo_mysql

# Копирование конфигурации PHP-FPM
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Создание директории для логов
RUN mkdir -p /var/log && touch /var/log/fpm-php.www.log

WORKDIR /var/www/html

# Копирование файлов приложения
COPY . .

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

CMD ["php-fpm", "-F", "-R"]
