#!/bin/sh
set -eu

cd "$(dirname "$0")"

if [ ! -f .env.production ]; then
    echo 'Create .env.production from .env.production.example and fill in its values first.' >&2
    exit 1
fi

compose() {
    sudo docker compose --env-file .env.production -f compose.production.yaml "$@"
}

compose config --quiet
compose build --pull
compose up -d postgres
compose run --rm app php artisan migrate --force
compose run --rm app php artisan db:seed --class=AdminUserSeeder --force
compose up -d app web
compose ps
