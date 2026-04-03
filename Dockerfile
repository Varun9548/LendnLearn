FROM php:8.2-apache

# Enable Apache mod_rewrite for routing
RUN a2enmod rewrite

# Install PostgreSQL dependencies and the PDO PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy application files to the Apache document root
COPY . /var/www/html/

# Ensure the cover images directory exists and is writable for book uploads
RUN mkdir -p /var/www/html/cover_img \
    && chown -R www-data:www-data /var/www/html/cover_img \
    && chmod -R 777 /var/www/html/cover_img

# Expose port 80
EXPOSE 80
