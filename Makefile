.PHONY: up down build bash composer

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

bash:
	docker exec -it twitch_recorder_php bash

composer:
	docker-compose exec php composer $(filter-out $@,$(MAKECMDGOALS))

phpstan:
	docker-compose exec php tools/vendor/bin/phpstan analyse

format:
	docker-compose exec php sh -c 'PHP_CS_FIXER_IGNORE_ENV=1 tools/vendor/bin/php-cs-fixer fix src'

test:
	docker-compose exec php vendor/bin/phpunit tests