# Stage 1: Build PHP dependencies
FROM php:8.4-fpm-alpine AS php-builder

# Install system dependencies & PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql pgsql zip opcache bcmath exif pcntl gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy only dependency definitions first to leverage Docker cache
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-interaction --no-autoloader --no-scripts --prefer-dist --ignore-platform-reqs

# Copy the rest of the application files
COPY . .

# Generate production autoloader
RUN composer dump-autoload --optimize --no-dev


# Stage 2: Compile assets
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json tailwind.config.js postcss.config.js vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm ci && npm run build


# Stage 3: Production environment
FROM php:8.4-fpm-alpine

# Install nginx and runtime dependencies
RUN apk add --no-cache nginx

# Install PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql pgsql zip opcache bcmath exif pcntl gd

WORKDIR /var/www/html

# Copy application files
COPY --from=php-builder --chown=www-data:www-data /var/www/html /var/www/html
# Copy compiled assets
COPY --from=node-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Setup startup entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Configure opcache for production
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Ensure directories exist and have proper permissions
RUN mkdir -p /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
