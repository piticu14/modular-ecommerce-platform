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

docs-build:
	docker compose exec -T api-php php artisan scramble:export

docs-clean:
	docker compose exec -T api-php rm -rf storage/api-docs

docs-debug:
	docker compose exec -T api-php php artisan scramble:export -vvv

check-frontend:
	docker compose exec -T frontend npm run lint
	docker compose exec -T frontend npm run ts-check
	docker compose exec -T frontend npm run format:check

fix-frontend:
	docker compose exec -T frontend npm run lint -- --fix
	docker compose exec -T frontend npm run format

check-service:
	docker compose exec $(service) ./vendor/bin/phpstan analyse --memory-limit=2G
	docker compose exec $(service) ./vendor/bin/pint
	docker compose exec $(service) ./vendor/bin/psalm

check:
	bash scripts/check.sh

check-fe:
	bash scripts/check.sh --frontend