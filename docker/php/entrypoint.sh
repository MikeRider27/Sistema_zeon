#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist
fi

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache

if [ -f .env ] && grep -q '^APP_KEY=$' .env; then
    echo "Generating application key..."
    php artisan key:generate --ansi --force
fi

echo "Waiting for database at ${DB_HOST:-db}:${DB_PORT:-5432}..."
until php -r "exit(@fsockopen('${DB_HOST:-db}', ${DB_PORT:-5432}) ? 0 : 1);"; do
    sleep 1
done
echo "Database is up."

php artisan migrate --force
php artisan storage:link || true

exec "$@"
