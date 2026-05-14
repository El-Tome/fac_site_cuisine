WITH_TRAEFIK = false

ifeq ($(WITH_TRAEFIK), true)
  COMPOSE = docker compose
else
  COMPOSE = docker compose -f compose.simple.yml
endif

PHP = $(COMPOSE) exec php

# --- Docker ---
up:
	$(COMPOSE) up -d
	$(PHP) composer install

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

logs:
	$(COMPOSE) logs -f

# --- Shell ---
shell:
	$(PHP) sh

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

.PHONY: up down build logs shell composer-install composer-update \
        cc migrate migration schema-update fixtures console
