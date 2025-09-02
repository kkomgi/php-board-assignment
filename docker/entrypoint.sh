#!/bin/bash
set -e

if [ ! -f .env ]; then
  cp .env.example .env
fi

composer install --no-interaction --prefer-dist

# DB 준비될 때까지 대기
echo "Waiting for MySQL ($DB_HOST)..."
until mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  sleep 3
done

# 준비되면 migrate
php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=8000
