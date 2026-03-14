#!/bin/sh

set -e

wait-for mysql-products 3306
wait-for rabbitmq 5672

echo "Services ready."

php artisan migrate --force --isolated || true

echo "Starting Outbox Worker..."

exec php artisan outbox:work
