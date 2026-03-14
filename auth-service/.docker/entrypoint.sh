#!/bin/sh

set -e

wait-for mysql-auth 3306

php artisan migrate --force

exec php-fpm
