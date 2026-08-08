FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies.
# Retry loop + --fix-missing makes the build resilient to transient Debian
# mirror hiccups (a single failed archive fetch would otherwise abort the build
# with apt exit 100). --no-install-recommends keeps the download set small.
RUN set -eux; \
    for i in 1 2 3; do \
        apt-get update && apt-get install -y --no-install-recommends --fix-missing \
            git unzip curl zip \
            libpng-dev libjpeg-dev libfreetype6-dev libicu-dev libzip-dev libonig-dev \
            cron supervisor nginx \
        && break || { echo "apt attempt $i failed; retrying in 5s"; apt-get clean; sleep 5; }; \
    done; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install gd pdo pdo_mysql intl zip opcache mbstring bcmath; \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer



# Override PHP settings for larger file uploads
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini


# Copy existing application directory contents
COPY . .

# Set permissions
# RUN chmod -R 775 storage bootstrap/cache


# Make sure storage dirs exist and are writable.
# storage/app/purifier holds HTMLPurifier's serialized definitions — its default
# location is inside vendor/, which www-data cannot write, and that made every
# frontend-section save fail.
RUN mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache \
        storage/app/purifier bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Create public upload directories and set permissions
RUN mkdir -p public/assets/images/temp public/Service_images public/files \
    && chmod -R 775 public/assets/images public/Service_images public/files \
    && chown -R www-data:www-data public/assets/images public/Service_images public/files \
    && chown -R www-data:www-data public/assets \
    && chmod -R 775 public/assets


COPY deployment/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh


# Install PHP dependencies with increased memory limit.
# --ignore-platform-reqs: a locked transitive dep (lcobucci/clock) caps at PHP
#   ~8.2 while this image is 8.3; the required PHP extensions are installed
#   above, so ignoring the *version* constraint is safe. Without it the build
#   fails with "requires php ~8.1.0 || ~8.2.0".
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-progress --no-interaction --ignore-platform-reqs 2>&1 || \
    (echo "=== Composer Install Failed ===" && \
     echo "=== PHP Extensions ===" && \
     php -m && \
     echo "=== PHP Info ===" && \
     php -i | grep -E "memory_limit|max_input|post_max_size" && \
     exit 1)

# Install Node.js & build assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm ci --no-audit --no-fund && npm run prod

    
# Nginx configuration
# COPY nginx/default.conf /etc/nginx/sites-available/default
# Supervisord configuration to manage nginx and php-fpm
# COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf


# Nginx configuration
COPY deployment/nginx/default.conf /etc/nginx/sites-available/default
# Supervisord configuration
COPY deployment/supervisor/supervisord.conf /etc/supervisor/supervisord.conf



# Create the storage link
RUN php artisan storage:link
    
# Expose ports for Nginx and PHP-FPM
EXPOSE 80 9000


ENTRYPOINT ["/entrypoint.sh"]

# Start supervisord to manage both Nginx and PHP-FPM
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
