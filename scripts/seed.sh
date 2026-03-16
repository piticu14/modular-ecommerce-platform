#!/bin/bash

set -e

SERVICES=("auth-php" "order-php" "product-php")

echo "Running seeders..."

for service in "${SERVICES[@]}"
do
  echo "Running seeders for $service."
  docker compose exec -T --user ${UID}:${GID} $service php artisan db:seed || true
done

echo "Seeding finished."