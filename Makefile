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

# ----------------------
# Бэкап и восстановление базы MySQL

DB_CONTAINER=elearning_db
DB_NAME=elearning_db
DB_USER=user
DB_PASS=password
BACKUP_FILE=elearning_backup.sql

# Создать бэкап базы
backup:
	docker exec $(DB_CONTAINER) sh -c 'exec mysqldump -u $(DB_USER) -p"$(DB_PASS)" $(DB_NAME)' > $(BACKUP_FILE)
	@echo "Backup saved to $(BACKUP_FILE)"

# Восстановить базу из бэкапа
restore:
	cat $(BACKUP_FILE) | docker exec -i $(DB_CONTAINER) mysql -u $(DB_USER) -p"$(DB_PASS)" $(DB_NAME)
	@echo "Database restored from $(BACKUP_FILE)"
