#!/usr/bin/env bash

set -euo pipefail

declare -A SERVICES=(
  ["api-php"]="api-gateway"
  ["auth-php"]="auth-service"
  ["order-php"]="order-service"
  ["product-php"]="product-service"
)

DB_SERVICES=("auth-php" "order-php" "product-php")

START_TIME=$(date +%s)

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

  local step_start=$(date +%s)

  print_step "$label"
  "$@"

  local step_end=$(date +%s)
  local duration=$((step_end - step_start))

  print_success "$label completed in $(format_time $duration)"
}

on_error() {
  print_error "Initialization failed"
  exit 1
}

trap on_error ERR

# ===== START =====
print_header "🚀 Project Initialization Started"

# ===== ENV FILES =====
print_header "📄 Environment Setup"

# Root .env
if [[ ! -f ".env" ]]; then
  run_step "Creating root .env file" cp .env.example .env
else
  print_success "Root .env already exists"
fi

# Service envs
for dir in "${SERVICES[@]}"; do
  if [[ ! -f "$dir/.env" ]]; then
    run_step "Creating .env for $dir" cp "$dir/.env.example" "$dir/.env"
  else
    print_success "$dir/.env already exists"
  fi
done

# Frontend env
if [[ ! -f "frontend/.env" ]]; then
  run_step "Creating frontend .env" cp frontend/.env.example frontend/.env
else
  print_success "frontend/.env already exists"
fi

# ===== DOCKER =====
print_header "🐳 Docker Setup"

run_step "Starting containers" docker compose up -d --build

# ===== COMPOSER =====
print_header "📦 Installing Dependencies"

for service in "${!SERVICES[@]}"; do
  echo
  printf "${BOLD}${CYAN}Service:${NC} %s\n" "$service"

  run_step "Installing composer dependencies" \
    docker compose exec -T "$service" composer install
done

# ===== APP KEYS =====
print_header "🔐 Generating App Keys"

for service in "${!SERVICES[@]}"; do
  run_step "Generating app key for $service" \
    docker compose exec -T "$service" php artisan key:generate --force
done

# ===== MIGRATIONS =====
print_header "🗄️ Database Migrations"

for service in "${DB_SERVICES[@]}"; do
  run_step "Running migrations for $service" \
    docker compose exec -T "$service" php artisan migrate:fresh --force
done

# ===== SUMMARY =====
END_TIME=$(date +%s)
TOTAL_DURATION=$((END_TIME - START_TIME))

echo
print_header "✅ Project initialized successfully"
printf "${GREEN}Total duration:${NC} %s\n" "$(format_time $TOTAL_DURATION)"