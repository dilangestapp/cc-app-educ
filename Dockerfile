(
echo FROM php:8.2-cli-alpine
echo WORKDIR /app
echo.
echo RUN apk add --no-cache bash git unzip icu-dev libzip-dev oniguruma-dev ^&^& docker-php-ext-install intl mbstring zip pdo pdo_mysql
echo COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
echo.
echo COPY . /app
echo.
echo RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts
echo.
echo RUN sed -i 's/\r$//' /app/start.sh ^&^& chmod +x /app/start.sh ^&^& mkdir -p storage bootstrap/cache ^&^& chmod -R 775 storage bootstrap/cache ^|^| true
echo.
echo EXPOSE 8080
echo CMD ["sh","-lc","./start.sh"]
) > Dockerfile
