FROM php:8.1-apache

# Install node and npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get update && apt-get install -y nodejs

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG PHP_MODE

RUN mv "$PHP_INI_DIR/php.ini-$PHP_MODE" "$PHP_INI_DIR/php.ini" && \
    sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    apt-get update -y && \
    apt-get install -y unzip && \
    docker-php-ext-install bcmath pdo_mysql && \
    a2enmod rewrite \