#!/bin/sh
set -e
PORT=${PORT:-8080}
echo "Starting on port $PORT"
sed -i "s/RAILWAY_PORT/$PORT/g" /etc/nginx/sites-available/default
nginx -t
php-fpm -D
sleep 1
nginx -g "daemon off;"
