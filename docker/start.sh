#!/bin/sh
set -e

cd /app

# ── Ensure storage directories exist ─────────────────────────
mkdir -p storage/framework/views \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/logs \
         bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# ── Storage symlink ───────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ── Wait for PostgreSQL to be ready (max 30s) ────────────────
echo "==> Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT:-5432}..."
TRIES=0
until php -r "
    \$dsn = 'pgsql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 5432) . ';dbname=' . getenv('DB_DATABASE');
    new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
" 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ $TRIES -ge 15 ]; then
        echo "ERROR: PostgreSQL not reachable after 30s. Check DB_HOST=${DB_HOST} DB_PORT=${DB_PORT:-5432}"
        exit 1
    fi
    echo "  not ready yet (attempt $TRIES/15), retrying in 2s..."
    sleep 2
done
echo "  PostgreSQL is ready."

# ── Laravel caches ───────────────────────────────────────────
echo "==> Caching config / routes / views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Migrations ───────────────────────────────────────────────
echo "==> Running migrations..."
php artisan migrate --force

# ── Seed admin user + categories (idempotent via firstOrCreate) ──
echo "==> Seeding admin user and categories..."
php artisan db:seed --force

echo "==> Verifying built assets..."
ls -la /app/public/build/ 2>/dev/null && ls -la /app/public/build/assets/ 2>/dev/null || echo "WARNING: public/build not found!"

echo "==> Configuring Nginx on port ${PORT:-8080}..."
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/nginx.conf

echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Starting Nginx..."
exec nginx -g 'daemon off;'
