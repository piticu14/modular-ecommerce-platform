#!/usr/bin/env sh
set -e

wait_for_tcp() {
  host=$1
  port=$2

  echo "Waiting for $host:$port..."

  while ! nc -z "$host" "$port"; do
    sleep 1
  done

  echo "$host:$port reachable"
}

wait_for_tcp mysql-orders 3306
wait_for_tcp redis 6379
wait_for_tcp rabbitmq 5672
echo "All dependencies ready"

exec php artisan outbox:work
