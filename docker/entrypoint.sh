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

# 4. Run migrations + seeders (stations, trips, buses, seats, admin user)
php artisan migrate --force
php artisan db:seed --force

echo "Laravel app is ready."

exec "$@"