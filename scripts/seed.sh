#!/usr/bin/env bash

set -euo pipefail

SERVICES=("auth-php" "order-php" "product-php")
START_TIME=$(date +%s)

CURRENT_SERVICE=""

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

print_warning() {
  printf "${YELLOW}⚠ %s${NC}\n" "$1"
}

format_time() {
  local seconds=$1
  printf "%02dm %02ds" $((seconds / 60)) $((seconds % 60))
}

run_seed() {
  local service="$1"
  local start=$(date +%s)

  CURRENT_SERVICE="$service"

  print_step "Seeding $service"

  if docker compose exec -T --user ${UID}:${GID} "$service" php artisan db:seed; then
    local end=$(date +%s)
    local duration=$((end - start))
    print_success "$service seeded in $(format_time $duration)"
  else
    print_warning "$service seeding failed (continuing...)"
  fi
}

# ===== START =====
print_header "🌱 Database Seeding Started"

for service in "${SERVICES[@]}"; do
  run_seed "$service"
done

# ===== SUMMARY =====
END_TIME=$(date +%s)
TOTAL_DURATION=$((END_TIME - START_TIME))

echo
print_header "✅ Seeding process finished"
printf "${GREEN}Total duration:${NC} %s\n" "$(format_time $TOTAL_DURATION)"