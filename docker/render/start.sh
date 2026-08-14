#!/bin/sh

set -eu

port="${PORT:-10000}"
sed "s/__PORT__/${port}/g" /etc/nginx/nginx.render.conf > /tmp/nginx.conf

if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="${APP_URL:-$RENDER_EXTERNAL_URL}"
    export ASSET_URL="${ASSET_URL:-$RENDER_EXTERNAL_URL}"
fi

php artisan config:clear
php artisan migrate --force

if [ "${RUN_DATABASE_SEEDER:-false}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan view:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
