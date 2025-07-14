# Dockerfile
FROM php:8.1-apache
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite
COPY task-manager/public/ /var/www/html/
COPY task-manager/includes/ /var/www/html/includes/
COPY task-manager/sql/ /var/www/html/sql/
RUN chown -R www-data:www-data /var/www/html/
EXPOSE 80
CMD ["apache2-foreground"]
