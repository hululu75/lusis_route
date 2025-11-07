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
    php83-iconv \
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

# Create www-data user if not exists (skip if already exists)
RUN set -x \
    && (addgroup -g 82 -S www-data 2>/dev/null || true) \
    && (adduser -u 82 -D -S -G www-data www-data 2>/dev/null || true)

# Create entrypoint script
RUN echo '#!/bin/bash' > /entrypoint.sh \
    && echo 'set -e' >> /entrypoint.sh \
    && echo '' >> /entrypoint.sh \
    && echo '# Check PHP extensions' >> /entrypoint.sh \
    && echo 'php -m | grep -i pdo' >> /entrypoint.sh \
    && echo 'php -m | grep -i sqlite' >> /entrypoint.sh \
    && echo '' >> /entrypoint.sh \
    && echo '# Create SQLite database if not exists' >> /entrypoint.sh \
    && echo 'if [ ! -f database/database.sqlite ]; then' >> /entrypoint.sh \
    && echo '    touch database/database.sqlite' >> /entrypoint.sh \
    && echo '    echo "Created SQLite database file"' >> /entrypoint.sh \
    && echo 'fi' >> /entrypoint.sh \
    && echo '' >> /entrypoint.sh \
    && echo '# Set permissions' >> /entrypoint.sh \
    && echo 'chown -R www-data:www-data /var/www/html' >> /entrypoint.sh \
    && echo 'chmod -R 755 /var/www/html/storage' >> /entrypoint.sh \
    && echo 'chmod -R 755 /var/www/html/bootstrap/cache' >> /entrypoint.sh \
    && echo 'chmod 664 database/database.sqlite 2>/dev/null || true' >> /entrypoint.sh \
    && echo '' >> /entrypoint.sh \
    && echo '# Run migrations' >> /entrypoint.sh \
    && echo 'php artisan migrate --force' >> /entrypoint.sh \
    && echo '' >> /entrypoint.sh \
    && echo '# Start server' >> /entrypoint.sh \
    && echo 'exec php artisan serve --host=0.0.0.0 --port=8000' >> /entrypoint.sh \
    && chmod +x /entrypoint.sh

# Set permissions for build-time files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
