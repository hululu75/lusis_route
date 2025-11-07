FROM php:8.4-cli

# Install system dependencies and PHP extensions in one go
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    # PHP extensions via apt (faster than compiling)
    php8.4-sqlite3 \
    php8.4-mysql \
    php8.4-pgsql \
    php8.4-mbstring \
    php8.4-gd \
    php8.4-bcmath \
    php8.4-curl \
    php8.4-xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
