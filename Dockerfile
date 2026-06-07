FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip \
    libpq-dev libzip-dev libxml2-dev \
    libonig-dev libpng-dev libjpeg-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql mbstring xml zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/sites-available/default

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

RUN nginx -t 2>&1 || true

CMD ["/start.sh"]
