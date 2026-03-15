#!/bin/sh

set -e

wait-for mysql-orders 3306
wait-for rabbitmq 5672

echo "Services ready."

php artisan migrate --force --isolated || true

#php artisan rabbit:setup

php artisan queue:work rabbitmq \
  --sleep=1 \
  --tries=1 \
  --timeout=90 \
  --max-jobs=1000
