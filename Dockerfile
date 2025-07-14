FROM php:8.1-apache

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files
COPY ./task-manager /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Configure Apache to listen on port 10000
ENV APACHE_PORT 10000
RUN sed -i "s/Listen 80/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${APACHE_PORT}/" /etc/apache2/sites-available/000-default.conf

# Expose the port Render expects
EXPOSE 10000

# Start Apache
CMD ["apache2-foreground"]