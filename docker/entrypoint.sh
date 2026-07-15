#!/bin/sh
set -e

cd /var/www

echo "==> Installing PHP dependencies..."
composer install --no-interaction --prefer-dist

echo "==> Installing frontend dependencies..."
npm ci

echo "==> Building frontend assets..."
npm run build

echo "==> Starting php-fpm..."
exec php-fpm
