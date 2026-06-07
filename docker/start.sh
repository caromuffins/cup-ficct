#!/bin/sh
PORT=${PORT:-8080}
sed -i "s/RAILWAY_PORT/$PORT/g" /etc/nginx/sites-available/default
php-fpm -D
nginx -g "daemon off;"
