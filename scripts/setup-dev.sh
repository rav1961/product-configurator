#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

cd "$ROOT_DIR"

git config core.hooksPath .githooks

chmod +x .githooks/pre-commit ,githooks/pre-push

docker compose up -d

docker compose exec -T php composer install
docker compose exec -T php php artisan config:clear
docker compose exec -T php php artisan migrate --force

echo "Development environment configured."
echo "Git hooks path: $(git config core.hooksPath)"