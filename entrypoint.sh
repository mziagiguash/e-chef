#!/bin/sh

# Ожидаем готовности базы данных
echo "Ожидание базы данных..."
while ! nc -z db 3306; do
  sleep 1
done
echo "База данных готова!"

# Устанавливаем зависимости если vendor отсутствует
if [ ! -d "vendor" ]; then
    echo "Установка Composer зависимостей..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
fi

# Копируем .env если не существует
if [ ! -f ".env" ]; then
    echo "Создание .env файла..."
    cp .env.example .env
fi

# Генерируем ключ приложения если нет
if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env)" = "APP_KEY=" ]; then
    echo "Генерация APP_KEY..."
    php artisan key:generate
fi

# Очищаем кеш конфигурации
php artisan config:clear
php artisan cache:clear

# Выполняем миграции
echo "Выполнение миграций..."
php artisan migrate --force

echo "Запуск PHP-FPM..."
exec php-fpm
