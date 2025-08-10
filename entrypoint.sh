#!/bin/sh

composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan config:clear
php artisan cache:clear
php artisan migrate --force
php-fpm
#!/bin/sh

# Ожидаем БД (опционально можно добавить wait-for-it или sleep 10)
# sleep 10

# Выполняем команды только при первом запуске
composer install --no-interaction --prefer-dist --optimize-autoloader

# Только если .env не существует — копируем
if [ ! -f ".env" ]; then
  cp .env.example .env
fi

php artisan config:clear
php artisan key:generate
php artisan migrate --force

