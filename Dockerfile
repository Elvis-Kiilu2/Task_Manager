FROM node:20-slim AS frontend-build
WORKDIR /app
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build

FROM php:8.4-cli

WORKDIR /var/www

# Install Official MySQL (default-mysql-server) and PHP extensions
RUN apt-get update && apt-get install -y \
    default-mysql-server \
    unzip git curl libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY backend/ .
RUN composer run-script post-autoload-dump --no-interaction

COPY --from=frontend-build /app/dist /var/www/public

# Setup permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy the startup script from the root context
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 10000

# Run the startup script
CMD ["/usr/local/bin/start.sh"]
