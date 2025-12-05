FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html/

# Set working dir
WORKDIR /var/www/html/

# Install composer packages (if vendor/ exists, this will skip)
RUN composer install --no-dev --optimize-autoloader || true

# Set permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
