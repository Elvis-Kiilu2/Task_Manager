FROM php:8.1-apache

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Apache to use index.php as the default DirectoryIndex
RUN echo "DirectoryIndex index.php" >> /etc/apache2/apache2.conf

# Copy project files
COPY ./task-manager/public/ /var/www/html/
COPY ./task-manager/includes/ /var/www/html/includes/
COPY ./task-manager/sql/ /var/www/html/sql/

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
