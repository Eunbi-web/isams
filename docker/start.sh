#!/bin/bash
set -e

PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf

php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec apache2-foreground
