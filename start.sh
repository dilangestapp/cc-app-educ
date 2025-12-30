#!/bin/sh
set -e

echo "=== Running migrations ==="
php artisan migrate --force || true

echo "=== Caching config/routes (optional) ==="
php artisan config:cache || true
php artisan route:cache || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:clear || true
php artisan route:clear || true

echo "=== Starting server ==="
exec php -S 0.0.0.0:${PORT:-8000} -t public
