FROM alpine:3.20

# Install PHP 8.3 and all extensions via apk - NO COMPILATION!
# Note: Alpine 3.20 stable has PHP 8.3, not 8.4 yet
RUN apk add --no-cache \
    php83 \
    php83-cli \
    php83-phar \
    php83-openssl \
    php83-mbstring \
    php83-tokenizer \
    php83-fileinfo \
    php83-curl \
    php83-xml \
    php83-xmlwriter \
    php83-simplexml \
    php83-dom \
    php83-pdo \
    php83-pdo_sqlite \
    php83-pdo_mysql \
    php83-pdo_pgsql \
    php83-sqlite3 \
    php83-gd \
    php83-bcmath \
    php83-zip \
    php83-session \
    php83-ctype \
    php83-pcntl \
    bash \
    git \
    curl \
    unzip \
    sqlite \
    && ln -sf /usr/bin/php83 /usr/bin/php

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader

# Create www-data user if not exists
RUN set -x \
    && addgroup -g 82 -S www-data \
    && adduser -u 82 -D -S -G www-data www-data

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
