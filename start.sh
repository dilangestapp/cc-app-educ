#!/bin/sh
set -e

echo "=== Preparing folders & permissions ==="
mkdir -p storage bootstrap/cache
chmod -R 775 storage bootstrap/cache || true

echo "=== Ensure storage symlink (public/storage) ==="
php artisan storage:link || true

echo "=== Clearing caches (SAFE) ==="
php artisan optimize:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "=== Running migrations ==="
php artisan migrate --force || true

echo "=== Optional caching (ONLY config) ==="
php artisan config:cache || true

echo "=== Starting server ==="
exec php -S 0.0.0.0:${PORT:-8080} -t public
