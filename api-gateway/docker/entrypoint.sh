#!/bin/sh

set -e

mkdir -p storage/logs bootstrap/cache

echo "Application ready."

if [ "$#" -gt 0 ]; then
  exec "$@"
fi

exec php-fpm
