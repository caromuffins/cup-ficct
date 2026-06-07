FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libxml2-dev \
    libonig-dev libpng-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql mbstring xml zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=$PORT
