FROM node:20-alpine AS frontend-build

WORKDIR /app
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build

FROM php:8.4-cli-alpine

WORKDIR /var/www

# Install MariaDB (MySQL alternative) and other dependencies
RUN apk add --no-cache unzip git curl libzip-dev mariadb mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip

# Prepare MariaDB directories
RUN mkdir -p /run/mysqld /var/lib/mysql && \
    chown -R mysql:mysql /run/mysqld /var/lib/mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY backend/ .
RUN composer run-script post-autoload-dump --no-interaction

COPY --from=frontend-build /app/dist /var/www/public

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy the startup script from the root context
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 10000

# Run the startup script
CMD ["/usr/local/bin/start.sh"]
