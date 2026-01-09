#!/bin/sh
set -e

echo "=== Preparing folders & permissions ==="
mkdir -p storage bootstrap/cache storage/app/public storage/tmp_uploads storage/app/tmp_imports
chmod -R 775 storage bootstrap/cache || true

echo "=== PHP upload limits (ini) ==="
# Le serveur PHP (built-in) respecte ces valeurs, c'est plus fiable que uniquement -d
mkdir -p /usr/local/etc/php/conf.d || true
cat > /usr/local/etc/php/conf.d/zz-uploads.ini << 'EOF'
upload_max_filesize=80M
post_max_size=80M
memory_limit=512M
max_execution_time=300
max_input_time=300
max_file_uploads=20
EOF

echo "=== Checking pdftotext (PDF import) ==="
command -v pdftotext || true
pdftotext -v || true

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
  -d upload_tmp_dir=/app/storage/tmp_uploads \
  -S 0.0.0.0:${PORT:-8080} -t public
