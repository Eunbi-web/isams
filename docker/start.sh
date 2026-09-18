#!/bin/bash
set -e

# Render routes external traffic to whichever port we listen on; keep Listen
# and the VirtualHost on the same port to avoid falling back to the default
# server document root (which serves 404s).
PORT="${PORT:-80}"
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec apache2-foreground
