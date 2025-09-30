#!/bin/sh

# Ждем немного чтобы база данных была готова
echo "Waiting for database..."
sleep 10

# Выполняем миграции
echo "Running migrations..."
php artisan migrate --force

# Запускаем приложение
echo "Starting application..."
php artisan serve --host=0.0.0.0 --port=8000
