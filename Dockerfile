FROM php:8.2-apache

COPY start.sh /start.sh
RUN chmod +x /start.sh

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zlib1g-dev libzip-dev \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /var/www/html/

WORKDIR /var/www/html
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data /var/www/html

#  OVERRIDE runtime
CMD ["/start.sh"]
