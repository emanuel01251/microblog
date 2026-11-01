#!/bin/sh

set -eu

cd /var/www/html

if [ -f artisan ] && [ -f vendor/autoload.php ]; then
    php artisan config:cache --no-ansi
fi

exec "$@"
