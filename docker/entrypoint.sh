#!/bin/bash
set -e

# This script runs every time the "app" container starts.
# It assumes the Laravel app code ALREADY EXISTS in this repo (you wrote
# it, it's in git). Its only job is to get that existing app into a
# running state with zero manual steps.

# 1. Create .env from the example if it doesn't exist yet
if [ ! -f .env ]; then
    echo "No .env found — copying from .env.example"
    cp .env.example .env
fi

# 1b. The source tree is bind-mounted over the image's vendor/. On a fresh
#     clone there's no host vendor/ yet, so install dependencies first.
if [ ! -f vendor/autoload.php ]; then
    echo "vendor/ not found — running composer install..."
    composer install --no-interaction --no-progress
fi

# 2. Generate the app key if it's not already set
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force
fi

# 3. Wait for MySQL to actually be ready to accept connections.
#    (depends_on + healthcheck in docker-compose.yml already helps with
#    this, but this loop is a second safety net for a cold start.)
echo "Waiting for database connection..."
until php artisan db:show > /dev/null 2>&1; do
    sleep 1
done
echo "Database is ready."

# 4. Run migrations. Idempotent — only pending ones apply.
php artisan migrate --force

# 5. Seed only on a first boot (empty database). Seeders here aren't
#    idempotent (e.g. SeatSeeder inserts), so re-running against existing
#    data would crash the container on every restart. And never in prod.
if [ "${APP_ENV:-local}" != "production" ]; then
    USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null)
    if [ "${USER_COUNT:-x}" = "0" ]; then
        echo "Database is empty — seeding..."
        php artisan db:seed --force
    else
        echo "Database already has data — skipping db:seed (use 'make fresh' to reseed)."
    fi
else
    echo "Skipping db:seed in production (APP_ENV=production)."
fi

echo "Laravel app is ready."

exec "$@"