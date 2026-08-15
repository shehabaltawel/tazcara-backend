.DEFAULT_GOAL := help

.PHONY: help up down restart logs ps
.PHONY: art migrate fresh seed shell composer

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
	@echo "                e.g. make art CMD=\"migrate:fresh --seed\""
	@echo "  migrate       Run artisan migrate inside the app container"
	@echo "  fresh         Run artisan migrate:fresh --seed"
	@echo "  shell         Open a shell inside the app container"
	@echo "  composer ARG=composer-args   Run composer (install/require)"
	@echo ""

up:
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

fresh:
	docker compose exec app php artisan migrate:fresh --seed

shell:
	docker compose exec app sh

composer:
	docker compose exec app composer $(ARG)