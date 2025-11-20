# Use PHP 8.2 CLI / FPM image
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
        zip unzip git curl libzip-dev libssl-dev pkg-config default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Copy application code
COPY .. .

COPY .env.example .env

## Copy .env if missing
#RUN if [ ! -f .env ]; then cp .env.example .env; fi
#
## Set permissions
#RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
#    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
#
## Install dependencies if vendor folder is missing
#RUN if [ ! -d vendor ]; then composer install --no-interaction --optimize-autoloader; fi
#
## Generate Laravel app key
#RUN php artisan key:generate
#
## Expose port for Laravel built-in server
#EXPOSE 8000
#
## Run Laravel built-in server
#CMD php artisan serve --host=0.0.0.0 --port=8000
