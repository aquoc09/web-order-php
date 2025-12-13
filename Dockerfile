FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install system dependencies for Composer + PHP zip extension
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

# Install composer packages
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

