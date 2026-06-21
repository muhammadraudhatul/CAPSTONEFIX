#!/usr/bin/env bash
set -e

echo "Clearing Laravel cache..."
php artisan optimize:clear || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Running migrations..."
php artisan migrate --force

if [ "$RUN_SEED" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force
else
    echo "Seeder skipped."
fi

echo "Caching Laravel config..."
php artisan config:cache

echo "Caching Laravel views..."
php artisan view:cache