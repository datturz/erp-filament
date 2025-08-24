FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    curl \
    freetype-dev \
    g++ \
    jpeg-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    make \
    mysql-client \
    nodejs \
    npm \
    oniguruma-dev \
    postgresql-dev \
    zip \
    unzip \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        mysqli \
        opcache \
        pdo \
        pdo_mysql \
        pcntl \
        zip

# Install Redis extension with correct Alpine packages
RUN apk add --no-cache --virtual .build-deps \
        autoconf \
        gcc \
        g++ \
        make \
        pkgconfig \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy package files first
COPY package*.json ./

# Install Node.js dependencies (including dev dependencies for build)
RUN rm -rf node_modules package-lock.json && npm install

# Copy all application code
COPY . .

# Install PHP dependencies after all files are copied
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Build the application
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# Copy PHP configuration (create if not exists)
COPY php.ini* /usr/local/etc/php/conf.d/

# Copy opcache configuration (create if not exists)
COPY opcache.ini* /usr/local/etc/php/conf.d/

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Start PHP-FPM
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]