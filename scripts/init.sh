#!/bin/bash

set -e

declare -A SERVICES=(
  ["api-php"]="api-gateway"
  ["auth-php"]="auth-service"
  ["order-php"]="order-service"
  ["product-php"]="product-service"
)
  DB_SERVICES=("auth-php" "order-php" "product-php")

echo "Creating env files..."

# Root .env
if [ ! -f ".env" ]; then
  echo "Creating docker-compose env file..."
  cp .env.example .env
fi

# Service .env
for dir in "${SERVICES[@]}"
do
  if [ ! -f "$dir/.env" ]; then
    echo "Creating env file for $dir..."
    cp "$dir/.env.example" "$dir/.env"
  fi
done

# Frontend .env
if [ ! -f "frontend/.env" ]; then
  echo "Creating frontend env file..."
  cp frontend/.env.example frontend/.env
fi

echo "Starting containers..."

docker compose up -d --build

echo "Installing composer dependencies..."

for service in "${!SERVICES[@]}"
do
  echo "Installing composer dependencies for $service..."
  docker compose exec -T "$service" composer install
done

echo "Generating app keys..."

for service in "${!SERVICES[@]}"
do
  echo "Generating app key for $service..."
  docker compose exec -T "$service" php artisan key:generate --force
done

echo "Running migrations..."

for service in "${DB_SERVICES[@]}"
do
  echo "Running migrations for $service..."
  docker compose exec -T "$service" php artisan migrate:fresh --force
done

echo "Project initialized!"