init:
	bash scripts/init.sh

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

logs:
	docker compose logs -f

composer:
	docker compose exec $(service) composer install

seed:
	bash scripts/seed.sh

test:
	bash scripts/run-tests.sh
