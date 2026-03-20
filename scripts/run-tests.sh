#!/usr/bin/env bash

set -euo pipefail

PROJECT_NAME="ecommerce_test"
START_TIME=$(date +%s)

CURRENT_STEP=""

# ===== COLORS =====
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# ===== HELPERS =====
print_line() {
  printf "${BLUE}============================================================${NC}\n"
}

print_header() {
  print_line
  printf "${BOLD}${CYAN}%s${NC}\n" "$1"
  print_line
}

print_step() {
  printf "${YELLOW}→ %s${NC}\n" "$1"
}

print_success() {
  printf "${GREEN}✓ %s${NC}\n" "$1"
}

print_error() {
  printf "${RED}✗ %s${NC}\n" "$1"
}

format_time() {
  local seconds=$1
  printf "%02dm %02ds" $((seconds / 60)) $((seconds % 60))
}

run_step() {
  local label="$1"
  shift

  CURRENT_STEP="$label"
  local start=$(date +%s)

  print_step "$label"
  "$@"

  local end=$(date +%s)
  local duration=$((end - start))

  print_success "$label completed in $(format_time $duration)"
}

on_error() {
  echo
  print_error "Failed during: ${CURRENT_STEP:-unknown step}"
  exit 1
}

trap on_error ERR

# ===== START =====
print_header "🧪 Test Environment Initialization"

run_step "Stopping previous Docker test stack" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml down -v

run_step "Stopping worker stack" \
  docker compose -p $PROJECT_NAME --profile worker -f docker-compose.yml -f docker-compose.test.yml down -v

run_step "Starting Docker test stack" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml up -d

# ===== WAIT FOR MYSQL =====
print_header "⏳ Waiting for services"

print_step "Waiting for MySQL (auth)"
until docker compose -p $PROJECT_NAME exec -T mysql-auth mysqladmin ping -h"localhost" --silent; do
  sleep 1
done
print_success "MySQL auth is ready"

print_step "Waiting for MySQL (orders)"
until docker compose -p $PROJECT_NAME exec -T mysql-orders mysqladmin ping -h"localhost" --silent; do
  sleep 1
done
print_success "MySQL orders ready"

print_step "Waiting for MySQL (products)"
until docker compose -p $PROJECT_NAME exec -T mysql-products mysqladmin ping -h"localhost" --silent; do
  sleep 1
done
print_success "MySQL products ready"

print_step "Waiting for Redis"
until docker compose -p $PROJECT_NAME exec -T redis redis-cli ping | grep PONG; do
  sleep 1
done
print_success "Redis ready"

print_step "Waiting for RabbitMQ"
until docker compose -p $PROJECT_NAME exec -T rabbitmq rabbitmq-diagnostics -q ping; do
  sleep 1
done
print_success "RabbitMQ ready"


# ===== SERVICE HEALTH =====
wait_for_service() {
  local name=$1
  local url=$2
  local max_attempts=30
  local attempt=0

  print_step "Waiting for $name"

  until docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T "$name" \
    sh -c "curl -fsS $url/health"; do

    attempt=$((attempt + 1))

    if [ $attempt -ge $max_attempts ]; then
      print_error "$name health check failed after $max_attempts attempts"
      docker compose -p $PROJECT_NAME logs "$name"
      exit 1
    fi

    sleep 2
  done

  print_success "$name is ready"
}

wait_for_service auth-php http://auth-nginx
wait_for_service order-php http://order-nginx
wait_for_service product-php http://product-nginx
wait_for_service api-php http://api-nginx

# ===== DATABASES =====
print_header "⚙️ Running Databases creations"
run_step "Reset testing database (auth)" \
  docker compose -p $PROJECT_NAME exec -T mysql-auth \
    mysql -uroot -proot -e "
      DROP DATABASE IF EXISTS auth_test;
      CREATE DATABASE auth_test;
      GRANT ALL PRIVILEGES ON auth_test.* TO 'auth'@'%';
      FLUSH PRIVILEGES;
    "

run_step "Reset testing database (orders)" \
  docker compose -p $PROJECT_NAME exec -T mysql-orders \
    mysql -uroot -proot -e "
      DROP DATABASE IF EXISTS orders_test;
      CREATE DATABASE orders_test;
      GRANT ALL PRIVILEGES ON orders_test.* TO 'orders'@'%';
      FLUSH PRIVILEGES;
    "

run_step "Reset testing database (products)" \
  docker compose -p $PROJECT_NAME exec -T mysql-products \
    mysql -uroot -proot -e "
      DROP DATABASE IF EXISTS products_test;
      CREATE DATABASE products_test;
      GRANT ALL PRIVILEGES ON products_test.* TO 'products'@'%';
      FLUSH PRIVILEGES;
    "

# ===== MIGRATIONS =====
print_header "⚙️ Running Migrations"

run_step "Auth migrations" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T auth-php php artisan migrate --env=testing --force

run_step "Create demo user" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T auth-php php artisan db:seed

run_step "Order migrations" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T order-php php artisan migrate --env=testing --force

run_step "Product migrations" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T product-php php artisan migrate --env=testing --force

# ===== STARING WORKERS AFTER MIGRATIONS =====
run_step "Starting background workers" \
  docker compose  -p $PROJECT_NAME --profile worker -f docker-compose.yml -f docker-compose.test.yml up -d

# ===== TESTS =====
print_header "🧪 Running Tests"

run_step "Auth Service tests" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T auth-php ./vendor/bin/phpunit tests/Feature

 run_step "Order Service tests" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T order-php ./vendor/bin/phpunit tests/Feature

run_step "Product Service tests" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T product-php ./vendor/bin/phpunit tests/Feature

run_step "API Gateway tests (Feature + E2E)" \
  docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T api-php ./vendor/bin/phpunit tests/Feature tests/E2E

# ===== CLEANUP =====
print_header "🧹 Cleanup"

run_step "Stopping test environment" \
# docker compose -p $PROJECT_NAME down -v

# ===== SUMMARY =====
END_TIME=$(date +%s)
TOTAL_DURATION=$((END_TIME - START_TIME))

echo
print_header "✅ All tests passed successfully"
printf "${GREEN}Total duration:${NC} %s\n" "$(format_time $TOTAL_DURATION)"