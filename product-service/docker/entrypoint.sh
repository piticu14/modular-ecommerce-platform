#!/bin/sh

set -e

mkdir -p storage/logs bootstrap/cache

echo "Waiting for dependencies..."

wait-for.sh mysql-products 3306
wait-for.sh rabbitmq 5672

echo "Application ready."

if [ "$#" -gt 0 ]; then
  exec "$@"
fi

exec php-fpm
