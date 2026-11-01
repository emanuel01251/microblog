FROM php:8.3.36-apache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/entrypoint.sh /usr/local/bin/microblog-entrypoint

ARG PHP_MODE

RUN mv "$PHP_INI_DIR/php.ini-$PHP_MODE" "$PHP_INI_DIR/php.ini" && \
    sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    apt-get update && \
    apt-get install -y --no-install-recommends libsqlite3-dev unzip && \
    docker-php-ext-install bcmath pdo_mysql pdo_sqlite && \
    a2enmod rewrite && \
    printf '%s\n' 'opcache.validate_timestamps=1' 'opcache.revalidate_freq=60' > /usr/local/etc/php/conf.d/zz-microblog-performance.ini && \
    rm -rf /var/lib/apt/lists/* && \
    chmod +x /usr/local/bin/microblog-entrypoint

ENTRYPOINT ["microblog-entrypoint"]
CMD ["apache2-foreground"]
