#!/bin/sh

composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
php-fpm
