FROM php:8.4-cli-alpine

# Install system dependencies and PHP extensions via apk (Alpine's package manager)
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    postgresql-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
