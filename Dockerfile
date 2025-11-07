FROM alpine:3.20

# Install PHP 8.4 and all extensions via apk - NO COMPILATION!
RUN apk add --no-cache \
    php84 \
    php84-cli \
    php84-phar \
    php84-openssl \
    php84-mbstring \
    php84-tokenizer \
    php84-fileinfo \
    php84-json \
    php84-curl \
    php84-xml \
    php84-xmlwriter \
    php84-simplexml \
    php84-dom \
    php84-pdo \
    php84-pdo_sqlite \
    php84-pdo_mysql \
    php84-pdo_pgsql \
    php84-sqlite3 \
    php84-gd \
    php84-bcmath \
    php84-zip \
    php84-session \
    php84-ctype \
    php84-pcntl \
    bash \
    git \
    curl \
    unzip \
    sqlite \
    && ln -sf /usr/bin/php84 /usr/bin/php

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
