# Stage 1: Build dependencies
FROM php:8.2-fpm AS build

# Install system packages
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create app directory
WORKDIR /var/www/html

# Copy source
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --no-dev

# Stage 2: Production image
FROM php:8.2-fpm

WORKDIR /var/www/html

# Copy build artifacts
COPY --from=build /var/www/html /var/www/html

# Copy existing entrypoint
COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
