#!/bin/sh
set -e

cd /var/www

echo "==> Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

echo "==> Installing frontend dependencies..."
npm ci

echo "==> Building frontend assets..."
npm run build

echo "==> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize || true

echo "==> Fixing storage permissions..."
chown -R www-data:www-data storage bootstrap/cache

echo "==> Starting php-fpm..."
exec php-fpm
