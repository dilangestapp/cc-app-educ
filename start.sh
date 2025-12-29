#!/bin/sh
set -eu

PORT="${PORT:-8080}"

echo "[BOOT] start.sh running"
echo "[BOOT] Listening on 0.0.0.0:${PORT}"

mkdir -p storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

if [ -f artisan ]; then
  php artisan package:discover --ansi || true
fi

exec php -S 0.0.0.0:"$PORT" -t public
