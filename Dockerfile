FROM php:8.2-apache

# Enable Apache mod_rewrite for routing
RUN a2enmod rewrite

# Install PostgreSQL dependencies, curl, and the required PHP extensions
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql curl

# Copy application files to the Apache document root
COPY . /var/www/html/

# Ensure Apache can read all files (fixes missing CSS/JS) and cover_img is writable
RUN chown -R www-data:www-data /var/www/html/ \
    && find /var/www/html/ -type d -exec chmod 755 {} \; \
    && find /var/www/html/ -type f -exec chmod 644 {} \; \
    && mkdir -p /var/www/html/cover_img \
    && chmod -R 777 /var/www/html/cover_img

# Expose port 80
EXPOSE 80
