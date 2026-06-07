#!/bin/sh
set -e
PORT=${PORT:-8080}
echo "Starting on port $PORT"
sed -i "s/RAILWAY_PORT/$PORT/g" /etc/nginx/sites-available/default
# Iniciar php-fpm en background
php-fpm &
# Esperar que php-fpm levante
sleep 2
# Iniciar nginx en foreground
exec nginx -g "daemon off;"
