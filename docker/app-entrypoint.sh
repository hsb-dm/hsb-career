#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ] || [ "$APP_KEY" = 'base64:REPLACE_WITH_A_RANDOM_32_BYTE_KEY' ]; then
    echo 'Set a unique APP_KEY in .env.production before starting the app.' >&2
    exit 1
fi

case "${APP_URL:-}" in
    https://*) ;;
    *) echo 'APP_URL must use the public HTTPS Tunnel hostname.' >&2; exit 1 ;;
esac

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
php artisan optimize

exec "$@"
