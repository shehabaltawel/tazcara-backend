.DEFAULT_GOAL := help

.PHONY: help up down restart logs ps
.PHONY: art migrate fresh seed shell composer guard-local
.PHONY: test pint dump

help:
	@echo "Usage: make <target>"
	@echo ""
	@echo "  up          docker compose up -d --build"
	@echo "  down        docker compose down"
	@echo "  restart     docker compose restart"
	@echo "  logs        docker compose logs -f app"
	@echo "  ps          docker compose ps"
	@echo ""
	@echo "  art CMD=...   Run artisan inside the app container"
	@echo "                e.g. make art CMD=\"route:list\""
	@echo "  migrate       Run artisan migrate inside the app container"
	@echo "  fresh         migrate:fresh --seed (refused unless APP_ENV=local)"
	@echo "  seed          db:seed (refused unless APP_ENV=local)"
	@echo "  shell         Open a shell inside the app container"
	@echo "  composer ARG=composer-args   Run composer (install/require)"
	@echo "  test          Run the Pest suite inside the app container"
	@echo "  pint          Run Laravel Pint (code style) inside the app container"
	@echo "  dump          Regenerate database/dumps/tazcara.sql from the live DB"
	@echo ""
	@echo "  'fresh' and 'seed' are destructive — they refuse to run when"
	@echo "  APP_ENV is not local/testing, so CI/CD can never wipe a"
	@echo "  production database. For deploys use: make migrate"

up:
	@if [ ! -f .env ]; then cp .env.example .env; echo "Created .env from .env.example"; fi
	docker compose up -d --build

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f app

ps:
	docker compose ps

art:
	docker compose exec app php artisan $(CMD)

migrate:
	docker compose exec app php artisan migrate

# Guard: refuses destructive commands unless the app is in local dev.
# Reads APP_ENV from .env (the same file CI/CD and deploys would use),
# so "make fresh"/"make seed" can never wipe a production database.
guard-local:
	@if ! grep -qE "^APP_ENV=(local|testing)" .env 2>/dev/null; then \
		echo "Refusing: this target wipes/rewrites data and only runs when APP_ENV=local."; \
		exit 1; \
	fi

fresh: guard-local
	docker compose exec app php artisan migrate:fresh --seed

seed: guard-local
	docker compose exec app php artisan db:seed

shell:
	docker compose exec app sh

composer:
	docker compose exec app composer $(ARG)

test:
	docker compose exec app php artisan test

pint:
	docker compose exec app ./vendor/bin/pint

# Dump the live database to the committed seed file, using the credentials
# from .env (the db container always talks to MySQL on 127.0.0.1:3306).
dump:
	@$(eval DB_PWD := $(shell awk -F= '/^DB_PASSWORD=/{sub(/^DB_PASSWORD=/, ""); gsub(/^["'"'"']|["'"'"']$$/, ""); print}' .env))
	@$(eval DB_USER := $(shell awk -F= '/^DB_USERNAME=/{print $$2}' .env))
	@$(eval DB_NAME := $(shell awk -F= '/^DB_DATABASE=/{print $$2}' .env))
	docker compose exec -e MYSQL_PWD="$(DB_PWD)" db sh -c \
		'mysqldump -h 127.0.0.1 -u "$(DB_USER)" "$(DB_NAME)" --skip-comments --no-tablespaces --single-transaction' \
		> database/dumps/tazcara.sql