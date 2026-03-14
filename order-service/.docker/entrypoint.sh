#!/bin/sh

set -e

wait-for mysql-orders 3306

php artisan migrate --force

exec php-fpm
