#!/bin/sh
set -e

echo "=== Running migrations ==="
php artisan migrate --force || true

echo "=== Caching config/routes (optional) ==="
php artisan config:cache || true
php artisan route:cache || true

echo "=== Starting server ==="
exec php -S 0.0.0.0:${PORT:-8000} -t public
