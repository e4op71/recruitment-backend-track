FROM php:8.3-fpm AS development

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM php:8.3-fpm-alpine AS production

RUN apk add --no-cache \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

COPY . .
COPY --from=development /var/www/html/vendor /var/www/html/vendor

# Ottimizza per produzione
RUN composer install --no-dev --optimize-autoloader

# Permessi corretti
RUN chown -R www-data:www-data /var/www/html/storage
