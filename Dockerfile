FROM php:8.2-cli-alpine

WORKDIR /app

# Dépendances système + Node (pour Vite build)
RUN apk add --no-cache bash git unzip icu-dev libzip-dev oniguruma-dev postgresql-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    nodejs npm

# Extensions PHP (gd + db)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install intl mbstring zip pdo pdo_mysql pdo_pgsql gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer deps Node d'abord (cache Docker)
COPY package*.json /app/
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

# Copier le code
COPY . /app

# Build front (Vite) => crée public/build/manifest.json
RUN npm run build

# Composer
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

# Permissions + start
RUN sed -i 's/\r$//' /app/start.sh && chmod +x /app/start.sh \
  && mkdir -p storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache || true

EXPOSE 8080
CMD ["sh","-lc","./start.sh"]
