COMPOSE        = docker compose
COMPOSE_SIMPLE = docker compose -f compose.simple.yml
PHP            = $(COMPOSE) exec php
PHP_SIMPLE     = $(COMPOSE_SIMPLE) exec php

# --- Traefik ---
up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

logs:
	$(COMPOSE) logs -f

# --- Simple (sans Traefik) ---
up-simple:
	$(COMPOSE_SIMPLE) up -d

down-simple:
	$(COMPOSE_SIMPLE) down

build-simple:
	$(COMPOSE_SIMPLE) build

logs-simple:
	$(COMPOSE_SIMPLE) logs -f

# --- Shell ---
shell:
	$(PHP) sh

shell-simple:
	$(PHP_SIMPLE) sh

# --- Composer ---
composer-install:
	$(PHP) composer install

composer-update:
	$(PHP) composer update

# --- Symfony ---
cc:
	$(PHP) php bin/console cache:clear

migrate:
	$(PHP) php bin/console doctrine:migrations:migrate --no-interaction

migration:
	$(PHP) php bin/console make:migration

schema-update:
	$(PHP) php bin/console doctrine:schema:update --force

fixtures:
	$(PHP) php bin/console doctrine:fixtures:load --no-interaction

console:
	$(PHP) php bin/console $(cmd)

# --- Symfony (simple) ---
cc-simple:
	$(PHP_SIMPLE) php bin/console cache:clear

migrate-simple:
	$(PHP_SIMPLE) php bin/console doctrine:migrations:migrate --no-interaction

console-simple:
	$(PHP_SIMPLE) php bin/console $(cmd)

.PHONY: up down build logs up-simple down-simple build-simple logs-simple \
        shell shell-simple composer-install composer-update \
        cc migrate migration schema-update fixtures console \
        cc-simple migrate-simple console-simple
