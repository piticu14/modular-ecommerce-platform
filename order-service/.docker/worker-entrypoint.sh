#!/bin/sh

set -e

echo "Waiting for RabbitMQ..."

while ! nc -z rabbitmq 5672; do
  sleep 1
done

echo "RabbitMQ ready"

php artisan migrate --force || true

#php artisan rabbit:setup

php artisan queue:work rabbitmq \
  --sleep=1 \
  --tries=3 \
  --timeout=90
