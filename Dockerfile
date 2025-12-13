FROM php:8.2-apache

# 🔥 FORCE clean MPM state (BẮT BUỘC)
RUN a2dismod mpm_event mpm_worker || true \
 && a2enmod mpm_prefork

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Copy composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app source
COPY . /var/www/html/

WORKDIR /var/www/html/

RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
