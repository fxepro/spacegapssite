#!/bin/sh
set -e

cd /app

echo "==> DB config: host=${DB_HOST} port=${DB_PORT:-5432} db=${DB_DATABASE} user=${DB_USERNAME}"

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

# ── Wait for PostgreSQL (max 60s) ─────────────────────────────
echo "==> Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT:-5432}..."
TRIES=0
until php -r "
    try {
        \$dsn = 'pgsql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 5432) . ';dbname=' . getenv('DB_DATABASE');
        new PDO(\$dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_TIMEOUT => 5]);
        echo 'ok';
    } catch (Exception \$e) {
        file_put_contents('php://stderr', \$e->getMessage() . PHP_EOL);
        exit(1);
    }
" 2>/tmp/pg_error; do
    TRIES=$((TRIES + 1))
    echo "  attempt $TRIES: $(cat /tmp/pg_error 2>/dev/null | head -1)"
    if [ $TRIES -ge 30 ]; then
        echo "ERROR: PostgreSQL unreachable after 60s. Last error: $(cat /tmp/pg_error 2>/dev/null)"
        echo "  DB_HOST=${DB_HOST} DB_PORT=${DB_PORT:-5432} DB_DATABASE=${DB_DATABASE}"
        exit 1
    fi
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

# ── Seed admin user + categories (idempotent) ────────────────
echo "==> Seeding..."
php artisan db:seed --force

# ── Assets check ─────────────────────────────────────────────
ls -la /app/public/build/assets/ 2>/dev/null || echo "WARNING: public/build/assets not found"

# ── Configure Nginx port ──────────────────────────────────────
echo "==> Starting on port ${PORT:-8080}..."
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/nginx.conf

php-fpm -D
exec nginx -g 'daemon off;'
