#!/usr/bin/env bash

set -euo pipefail

SERVICES=("api-php" "auth-php" "order-php" "product-php")
RUN_FRONTEND=false
CURRENT_SERVICE=""
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
  local exit_code=$?
  echo
  print_error "Check failed on service: ${CURRENT_SERVICE:-unknown}"
  exit "$exit_code"
}

trap on_error ERR

# ===== ARGUMENTS =====
for arg in "$@"; do
  case "$arg" in
    --frontend)
      RUN_FRONTEND=true
      ;;
    --help|-h)
      echo "Usage:"
      echo "  bash scripts/check.sh            # backend only"
      echo "  bash scripts/check.sh --frontend # backend + frontend"
      exit 0
      ;;
    *)
      print_error "Unknown argument: $arg"
      echo "Use --help"
      exit 1
      ;;
  esac
done

# ===== START =====
print_header "🚀 Project Check Started"

print_step "Backend services: ${SERVICES[*]}"
if [[ "$RUN_FRONTEND" == true ]]; then
  print_step "Frontend: enabled"
else
  print_step "Frontend: disabled"
fi

echo

# ===== BACKEND =====
print_header "🧩 Backend Checks"

for service in "${SERVICES[@]}"; do
  CURRENT_SERVICE="$service"

  echo
  printf "${BOLD}${CYAN}Service:${NC} %s\n" "$service"

  run_step "PHPStan analysis" \
    docker compose exec "$service" ./vendor/bin/phpstan analyse --memory-limit=2G

  run_step "Code style (Pint)" \
    docker compose exec "$service" ./vendor/bin/pint

  run_step "Static analysis (Psalm)" \
    docker compose exec "$service" ./vendor/bin/psalm

  print_success "Service ${service} passed all checks"
done

CURRENT_SERVICE=""

# ===== FRONTEND =====
if [[ "$RUN_FRONTEND" == true ]]; then
  echo
  print_header "🎨 Frontend Checks"

  run_step "ESLint" \
    docker compose exec -T frontend npm run lint

  run_step "TypeScript check" \
    docker compose exec -T frontend npm run ts-check

  run_step "Format check" \
    docker compose exec -T frontend npm run format:check

  print_success "Frontend passed all checks"
fi

# ===== SUMMARY =====
END_TIME=$(date +%s)
TOTAL_DURATION=$((END_TIME - START_TIME))

echo
print_header "✅ All checks completed successfully"
printf "${GREEN}Total duration:${NC} %s\n" "$(format_time $TOTAL_DURATION)"