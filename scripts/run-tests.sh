#!/bin/bash

# Exit on error
set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'
PROJECT_NAME="ecommerce_test"

echo -e "${BLUE}Starting isolated testing environment...${NC}"

# Start the stack with test overrides
# Note: This uses the existing mysql containers but will create _test databases if they don't exist
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml up -d

echo -e "${BLUE}Waiting for MySQL and services to be ready...${NC}"
# Simple wait loop for mysql containers
until docker compose -p $PROJECT_NAME exec -T mysql-auth mysqladmin ping -h"localhost" --silent; do
    sleep 1
done

# Wait for PHP-FPM services to be ready to handle connections
wait_for_service() {
      local name=$1
      local url=$2

    echo -e "${BLUE}Waiting for $name to be ready...${NC}"

    until docker compose -p $PROJECT_NAME exec -T $name curl -f "$url/health" > /dev/null 2>&1; do
        sleep 1
    done

}

wait_for_service auth-php http://auth-nginx
wait_for_service order-php http://order-nginx
wait_for_service product-php http://product-nginx
wait_for_service api-php http://api-nginx

echo -e "${BLUE}Preparing test databases...${NC}"
# Create databases if they don't exist (since init script only runs once per volume lifecycle)
docker compose -p $PROJECT_NAME exec -T mysql-auth mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS auth_test;"
docker compose -p $PROJECT_NAME exec -T mysql-orders mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS orders_test;"
docker compose -p $PROJECT_NAME exec -T mysql-products mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS products_test;"

# We need to run migrations on the test databases
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T auth-php php artisan migrate:fresh --force
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T order-php php artisan migrate:fresh --force
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T product-php php artisan migrate:fresh --force

echo -e "${GREEN}Environment ready. Running tests...${NC}"

echo -e "${BLUE}Running Auth Service Tests...${NC}"
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T auth-php ./vendor/bin/phpunit tests/Feature

echo -e "${BLUE}Running Order Service Tests...${NC}"
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T order-php ./vendor/bin/phpunit tests/Feature

echo -e "${BLUE}Running Product Service Tests...${NC}"
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T product-php ./vendor/bin/phpunit tests/Feature

echo -e "${BLUE}Running API Gateway Tests (including E2E)...${NC}"
docker compose -p $PROJECT_NAME -f docker-compose.yml -f docker-compose.test.yml exec -T api-php ./vendor/bin/phpunit tests/Feature tests/E2E

echo -e "${GREEN}All tests completed!${NC}"

# Optional: Stop the test environment

echo -e "${BLUE}Stopping testing environment...${NC}"
docker compose -p $PROJECT_NAME  -f docker-compose.yml -f docker-compose.test.yml down