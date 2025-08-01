# Makefile для Laravel + Docker

# Поднять проект
up:
	docker-compose up -d

# Остановить и удалить все контейнеры
stop:
	docker-compose down

# Пересобрать образы и запустить контейнеры
rebuild:
	docker-compose down
	docker-compose build --no-cache
	docker-compose up -d

# Запустить миграции
migrate:
	docker exec -it elearning_api php artisan migrate

# Очистить кэш Laravel
cache-clear:
	docker exec -it elearning_api php artisan config:clear
	docker exec -it elearning_api php artisan cache:clear

# Просмотр логов Laravel
logs:
	docker logs -f elearning_api

# Получить доступ в контейнер Laravel
sh:
	docker exec -it elearning_api sh
