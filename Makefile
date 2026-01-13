# ==== Config ====
SHELL := /bin/bash

# Compose files
DC_DEV   = docker compose -f docker-compose.yml
DC_PROD  = docker compose -f docker-compose.prod.yml

# Default to dev (override with: DC="$(DC_PROD)")
DC ?= $(DC_DEV)

# Services
PHP_SVC   = php
NGINX_SVC = nginx
DB_SVC    = db
REDIS_SVC = redis
RMQ_SVC   = rabbitmq
MAIL_SVC  = mailhog

# In CI / GitHub Actions (SSH): no TTY => pass EXEC_TTY=-T
EXEC_TTY ?=

# Commands
CONSOLE   = $(DC) exec $(EXEC_TTY) $(PHP_SVC) bin/console
COMPOSER  = $(DC) exec $(EXEC_TTY) $(PHP_SVC) composer
PHP       = $(DC) exec $(EXEC_TTY) $(PHP_SVC) php

# ==== Aide ====
.PHONY: help
help:
	@echo "Cibles disponibles :"
	@echo "  up                 - Build & démarre les services (dev par défaut)"
	@echo "  build              - Build les images"
	@echo "  start/stop         - Démarre / stoppe les services"
	@echo "  down               - Stoppe et supprime les conteneurs"
	@echo "  restart            - Redémarre les services (sans down)"
	@echo "  ps                 - Liste les services"
	@echo "  logs               - Suivi des logs (tous services)"
	@echo "  php                - Shell dans le conteneur PHP"
	@echo "  composer           - composer install"
	@echo "  cache-clear        - Vide le cache Symfony"
	@echo "  cache-warmup       - Warmup du cache Symfony"
	@echo "  c                  - Ouvre la console Symfony (bin/console)"
	@echo "  migrate/diff       - Migrations doctrine"
	@echo "  db-shell           - Shell SQL (psql) dans la DB"
	@echo "  db-reset           - Drop + Create + Migrate (DANGER, DEV)"
	@echo "  fixtures           - Charge les fixtures (si présentes)"
	@echo "  consume            - Lance le worker Messenger (async)"
	@echo "  qa                 - cs + phpstan + phpunit"
	@echo "  cs/cs-fix/stan/test- Qualité et tests unitaires"
	@echo "  prune              - Nettoie volumes/images non utilisés"
	@echo "  git-pull           - Reset hard sur origin/main"
	@echo "  deploy-prod        - Déploie main en PROD (compose prod + migrate + warmup)"
	@echo ""
	@echo "Astuce:"
	@echo "  PROD: make deploy-prod EXEC_TTY=-T"
	@echo "  DEV : make up"

# ==== Lifecycle Docker ====
.PHONY: up build start stop down restart ps logs
up:
	$(DC) up -d --build

build:
	$(DC) build --pull

start:
	$(DC) start

stop:
	$(DC) stop

down:
	$(DC) down

# restart sans down => moins de downtime
restart:
	$(DC) up -d --build

ps:
	$(DC) ps

logs:
	$(DC) logs -f

# ==== Dev utils ====
.PHONY: php composer
php:
	$(DC) exec $(PHP_SVC) sh

composer:
	$(COMPOSER) install --no-interaction --prefer-dist

# ==== Cache Symfony ====
.PHONY: cache-clear cache-warmup
cache-clear:
	$(CONSOLE) cache:clear

cache-warmup:
	$(CONSOLE) cache:warmup

# ==== Symfony / Doctrine ====
.PHONY: c migrate diff db-shell db-reset fixtures
c:
	$(CONSOLE)

migrate:
	$(CONSOLE) doctrine:migrations:migrate -n

diff:
	$(CONSOLE) make:migration

db-shell:
	$(DC) exec -e PGPASSWORD=symfony $(DB_SVC) psql -U symfony -d app

db-reset:
	$(CONSOLE) doctrine:database:drop --force --if-exists
	$(CONSOLE) doctrine:database:create
	$(CONSOLE) doctrine:migrations:migrate -n

fixtures:
	$(CONSOLE) doctrine:fixtures:load -n

# ==== Messenger ====
.PHONY: consume
consume:
	$(CONSOLE) messenger:consume async -vv

# ==== Qualité & tests ====
.PHONY: qa cs cs-fix stan test
qa: cs stan test

cs:
	$(PHP) -d memory_limit=-1 vendor/bin/php-cs-fixer fix --dry-run --diff --allow-risky=yes || true

cs-fix:
	$(PHP) -d memory_limit=-1 vendor/bin/php-cs-fixer fix

stan:
	$(PHP) -d memory_limit=-1 vendor/bin/phpstan analyse --no-progress

test: db-test migrate-test phpunit

.PHONY: db-test migrate-test phpunit

db-test:
	$(CONSOLE) doctrine:database:create --env=test --if-not-exists

migrate-test:
	$(CONSOLE) doctrine:migrations:migrate --env=test -n

phpunit:
	$(PHP) -d memory_limit=-1 bin/phpunit

# ==== Nettoyage ====
.PHONY: prune
prune:
	$(DC) down -v --remove-orphans
	docker system prune -f

# ==== Deploy ====
.PHONY: git-pull deploy-prod

git-pull:
	@echo "== Git sync (origin/main) =="
	git fetch --all --prune
	git reset --hard origin/main

deploy-prod: git-pull
	@echo "== Compose up PROD (build) =="
	$(MAKE) up DC="$(DC_PROD)"

	@echo "== Migrations (PROD) =="
	$(MAKE) migrate DC="$(DC_PROD)"

	@echo "== Cache clear (PROD) =="
	$(CONSOLE) cache:clear --env=prod

	@echo "== Restart PHP (flush OPCache) =="
	$(DC_PROD) restart $(PHP_SVC)

	@echo "✅ Deploy PROD terminé"
