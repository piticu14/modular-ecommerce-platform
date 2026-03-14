#!/bin/sh

set -e

wait-for mysql-products 3306

php artisan migrate --force || true

if [ "$#" -gt 0 ]; then
  exec "$@"
fi

exec php-fpm
