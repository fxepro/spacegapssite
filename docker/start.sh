#!/bin/sh
set -e

cd /app

# ── Persistent disk: ensure SQLite file exists ────────────────
# Render mounts the persistent disk at /var/data.
# If DB_DATABASE points there, create the file if it doesn't exist yet.
DB_PATH="${DB_DATABASE:-/var/data/database.sqlite}"
DB_DIR=$(dirname "$DB_PATH")

if [ ! -d "$DB_DIR" ]; then
    mkdir -p "$DB_DIR"
fi
if [ ! -f "$DB_PATH" ]; then
    echo "==> Creating SQLite database at $DB_PATH..."
    touch "$DB_PATH"
fi

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

# ── Laravel caches ───────────────────────────────────────────
echo "==> Caching config / routes / views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Migrations ───────────────────────────────────────────────
echo "==> Running migrations..."
php artisan migrate --force

# ── Seed on first deploy (empty users table) ─────────────────
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> Seeding initial admin user..."
    php artisan db:seed --force
fi

echo "==> Verifying built assets..."
ls -la /app/public/build/ 2>/dev/null && ls -la /app/public/build/assets/ 2>/dev/null || echo "WARNING: public/build not found!"

echo "==> Configuring Nginx on port ${PORT:-8080}..."
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/nginx.conf

echo "==> Starting PHP-FPM..."
php-fpm -D

echo "==> Starting Nginx..."
exec nginx -g 'daemon off;'
