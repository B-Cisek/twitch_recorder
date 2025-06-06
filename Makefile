.PHONY: up build composer

up:
	docker-compose up -d

build:
	docker-compose build

composer:
	docker-compose exec php composer $(filter-out $@,$(MAKECMDGOALS))