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