FROM php:8.3-fpm-alpine

# ── System deps ──────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    sqlite \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    curl

# ── PHP extensions ───────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    pdo \
    pdo_sqlite \
    bcmath \
    gd \
    zip \
    mbstring \
    opcache \
    xml

# ── Composer ─────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ── PHP dependencies (cached layer) ──────────────────────────
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ── Node dependencies + build (cached layer) ─────────────────
COPY package.json package-lock.json vite.config.js ./
RUN npm ci

# ── Full source ───────────────────────────────────────────────
COPY . .

# ── Build frontend assets ─────────────────────────────────────
RUN npm run build

# ── Laravel bootstrap ─────────────────────────────────────────
RUN php artisan package:discover --ansi 2>/dev/null || true
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ── Config & start scripts ───────────────────────────────────
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
