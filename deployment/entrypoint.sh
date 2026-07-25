#!/bin/bash

echo "=========================================="
echo "  Humucare Deployment Entrypoint"
echo "=========================================="

# Fix permissions (create dirs if missing)
mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/views /var/www/html/storage/framework/sessions /var/www/html/storage/framework/cache /var/www/html/bootstrap/cache /var/www/html/public/assets /var/www/html/public/Service_images /var/www/html/public/files
# Ensure Laravel log file exists and is writable by php-fpm user.
touch /var/www/html/storage/logs/laravel.log
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/assets /var/www/html/public/Service_images /var/www/html/public/files
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/assets /var/www/html/public/Service_images /var/www/html/public/files
chmod 664 /var/www/html/storage/logs/laravel.log

# ---- Wait for database ----
echo "[1/4] Waiting for database connection..."
max_tries=30
count=0
until php artisan migrate:status > /dev/null 2>&1; do
  count=$((count+1))
  if [ $count -ge $max_tries ]; then
    echo "  ✗ Database not ready after ${max_tries} attempts, starting anyway..."
    break
  fi
  echo "  Waiting for database... (attempt $count/$max_tries)"
  sleep 2
done
echo "  ✓ Database connected"

# ---- Run pending migrations ----
echo "[2/4] Checking for pending migrations..."
PENDING=$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || true)
if [ "$PENDING" -gt 0 ]; then
  echo "  Found $PENDING pending migration(s), running..."
  php artisan migrate --force 2>&1 || echo "  ✗ Migration failed, continuing..."
  echo "  ✓ Migrations complete"
else
  echo "  ✓ No pending migrations"
fi

# ---- Run seeders (OPT-IN ONLY) ----
# WARNING: FoodieSeeder is DESTRUCTIVE — it clears and re-inserts
# settings/menu/products. Running it on a live deploy would wipe the
# restaurant's real data. So it only runs when RUN_SEEDERS=true, which you
# set once on the very first install and never again.
echo "[3/4] Database seeders..."
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
  echo "  RUN_SEEDERS=true — seeding (fresh install)..."
  php artisan db:seed --force 2>&1 && echo "  ✓ Seeders complete" || echo "  ✗ Seeding failed, continuing..."
else
  echo "  ✓ Skipped (set RUN_SEEDERS=true for a first-time install only)"
fi

# ---- Cache config/routes/views ----
echo "[4/4] Caching configuration..."
php artisan config:cache 2>&1 || echo "  ✗ Config cache failed"
php artisan route:cache 2>&1 || echo "  ✗ Route cache failed"
php artisan view:cache 2>&1 || echo "  ✗ View cache failed"
echo "  ✓ Caches rebuilt"

echo "=========================================="
echo "  Startup complete — launching app"
echo "=========================================="

exec "$@"
