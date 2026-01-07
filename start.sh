#!/bin/sh
set -e

echo "=== Preparing folders & permissions ==="
mkdir -p storage bootstrap/cache
chmod -R 775 storage bootstrap/cache || true

echo "=== Clearing caches (SAFE) ==="
php artisan optimize:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "=== Running migrations ==="
php artisan migrate --force || true

echo "=== Optional caching (ONLY config) ==="
# ✅ config:cache est ok
php artisan config:cache || true

# ❌ IMPORTANT : on ne fait PAS route:cache sur Railway tant que tu as eu des soucis /register
# php artisan route:cache || true

echo "=== Starting server ==="
exec php -S 0.0.0.0:${PORT:-8080} -t public
