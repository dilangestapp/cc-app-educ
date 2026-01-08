#!/bin/sh
set -e

echo "=== Preparing folders & permissions ==="
mkdir -p storage bootstrap/cache storage/app/public storage/tmp_uploads
chmod -R 775 storage bootstrap/cache || true

echo "=== Ensure storage link ==="
php artisan storage:link || true

echo "=== Clearing caches (SAFE) ==="
php artisan optimize:clear || true

echo "=== Running migrations ==="
php artisan migrate --force || true

echo "=== Optional caching (ONLY config) ==="
php artisan config:cache || true

echo "=== Starting server ==="
exec php \
  -d upload_max_filesize=80M \
  -d post_max_size=80M \
  -d memory_limit=256M \
  -d max_execution_time=300 \
  -d max_input_time=300 \
  -d max_file_uploads=20 \
  -d upload_tmp_dir=/app/storage/tmp_uploads \
  -S 0.0.0.0:${PORT:-8080} -t public
