.PHONY: up down build composer

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

composer:
	docker-compose exec php composer $(filter-out $@,$(MAKECMDGOALS))