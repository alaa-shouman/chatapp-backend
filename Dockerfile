# Stage 1: Composer build
FROM composer:2.7 AS composer_builder

WORKDIR /app

# Copy composer files first for caching
COPY composer.* ./

# Install PHP deps without running scripts yet
RUN composer install --no-scripts --prefer-dist --optimize-autoloader

# Copy the full application
COPY . .

# Now run post-install scripts (needs artisan, configs, etc.)
RUN composer run-script post-install-cmd || true


# Stage 2: PHP-FPM final
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# System deps
RUN apk add --no-cache \
    git curl libpng-dev libjpeg-turbo-dev freetype-dev icu-dev \
    libzip-dev postgresql-dev $PHPIZE_DEPS openssl-dev

# Copy Composer from builder stage
COPY --from=composer_builder /usr/bin/composer /usr/local/bin/composer

# Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip exif pcntl bcmath opcache intl

# Copy only vendor from builder
COPY --from=composer_builder /app/vendor ./vendor

# Copy app code
COPY . .

# Remove any cached config generated in the builder and ensure runtime config will be used
RUN rm -f bootstrap/cache/config.php bootstrap/cache/services.php || true \
    && if [ -f artisan ]; then php artisan config:clear || true; fi

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
