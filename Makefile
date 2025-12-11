# ==== Config ====
DC        = docker compose
PHP_SVC   = php
NGINX_SVC = nginx
DB_SVC    = db
REDIS_SVC = redis
RMQ_SVC   = rabbitmq
MAIL_SVC  = mailhog

CONSOLE   = $(DC) exec $(PHP_SVC) bin/console
COMPOSER  = $(DC) exec $(PHP_SVC) composer
PHP       = $(DC) exec $(PHP_SVC) php

# ==== Aide ====
.PHONY: help
help:
	@echo "Cibles disponibles :"
	@echo "  up           - Build & démarre les services"
	@echo "  build        - Build les images"
	@echo "  start/stop   - Démarre / stoppe les services"
	@echo "  down         - Stoppe et supprime les conteneurs"
	@echo "  restart      - Redémarre les services"
	@echo "  ps           - Liste les services"
	@echo "  logs         - Suivi des logs (tous services)"
	@echo "  php          - Shell dans le conteneur PHP"
	@echo "  composer i   - composer install"
	@echo "  cache-clear  - Vide le cache Symfony"
	@echo "  c,migrate    - console & migrations"
	@echo "  db-shell     - Shell SQL (psql) dans la DB"
	@echo "  db-reset     - Drop + Create + Migrate (DANGER, DEV)"
	@echo "  fixtures     - Charge les fixtures (si présentes)"
	@echo "  consume      - Lance le worker Messenger (async)"
	@echo "  qa           - cs-check + phpstan + phpunit"
	@echo "  cs, stan, test - Qualité et tests unitaires"
	@echo "  prune        - Nettoie volumes/images non utilisés"

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

restart:
	$(DC) down && $(DC) up -d --build

ps:
	$(DC) ps

logs:
	$(DC) logs -f

# ==== Dev utils ====
.PHONY: sh composer cache-clear
php:
	$(DC) exec $(PHP_SVC) sh

composer:
	$(COMPOSER) install --no-interaction --prefer-dist

cache-clear:
	$(CONSOLE) cache:clear

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
.PHONY: qa cs stan test
qa: cs stan test

cs:
	$(PHP) -d memory_limit=-1 vendor/bin/php-cs-fixer fix --dry-run --diff --allow-risky=yes || true

cs-fix:
	$(PHP) -d memory_limit=-1 vendor/bin/php-cs-fixer fix

stan:
	$(PHP) -d memory_limit=-1 vendor/bin/phpstan analyse --no-progress

test:
	$(PHP) -d memory_limit=-1 bin/phpunit
# ==== Nettoyage ====
.PHONY: prune
prune:
	$(DC) down -v --remove-orphans
	docker system prune -f
