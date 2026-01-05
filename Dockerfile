FROM php:8.2-cli-alpine

WORKDIR /app

RUN apk add --no-cache bash git unzip icu-dev libzip-dev oniguruma-dev postgresql-dev \
  freetype-dev libjpeg-turbo-dev libpng-dev \
  poppler-utils \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install intl mbstring zip pdo pdo_mysql pdo_pgsql gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /app

RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

RUN sed -i 's/\r$//' /app/start.sh && chmod +x /app/start.sh \
  && mkdir -p storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache || true

EXPOSE 8080

CMD ["sh","-lc","./start.sh"]
