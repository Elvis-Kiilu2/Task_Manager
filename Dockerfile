# Use official PHP image with Apache
FROM php:8.1-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy app files
COPY task-manager/public/ /var/www/html/
COPY task-manager/includes/ /var/www/html/includes/

# Permissions (optional)
RUN chown -R www-data:www-data /var/www/html/

# Expose HTTP port
EXPOSE 80
