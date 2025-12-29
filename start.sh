(
echo #!/bin/sh
echo set -eu
echo.
echo PORT="${PORT:-8080}"
echo.
echo echo "[BOOT] start.sh running"
echo echo "[BOOT] Listening on 0.0.0.0:${PORT}"
echo.
echo mkdir -p storage bootstrap/cache ^|^| true
echo chmod -R 775 storage bootstrap/cache ^|^| true
echo.
echo ^# package discover (safe)
echo if [ -f artisan ]; then
echo   php artisan package:discover --ansi ^|^| true
echo fi
echo.
echo exec php -S 0.0.0.0:"$PORT" -t public
) > start.sh
