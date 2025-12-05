FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install system packages: unzip + git
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions (zip included)
RUN docker-php-ext-install mysqli pdo pdo_mysql zip

# Copy composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html/

WORKDIR /var/www/html/

# Install composer packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
