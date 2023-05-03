#!/bin/bash

if [ ! -f vendor/autoload.php ]; then
    echo "Installing dependencies..."
    composer install --no-progress --no-interaction
fi

php artisan key:generate
php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan app:collect-data
php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
exec docker-php-entrypoint "$@"