FROM php:8.1-fpm-alpine

# Install Apache and configure with PHP-FPM
RUN apk add --no-cache apache2 \
    && sed -i '/LoadModule rewrite_module/s/^#//g' /etc/apache2/httpd.conf \
    && echo "LoadModule proxy_module modules/mod_proxy.so" >> /etc/apache2/httpd.conf \
    && echo "LoadModule proxy_fcgi_module modules/mod_proxy_fcgi.so" >> /etc/apache2/httpd.conf

# Install node and npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get update && apt-get install -y nodejs

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG PHP_MODE

RUN mv "$PHP_INI_DIR/php.ini-$PHP_MODE" "$PHP_INI_DIR/php.ini" && \
    sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    apt-get update -y && \
    apt-get install -y \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
    bcmath \
    pdo_mysql \
    opcache \
    zip \
    intl \
    && a2enmod \
    rewrite \
    headers \
    expires \
    && pecl install redis \
    && docker-php-ext-enable redis

# Add custom php.ini settings
RUN echo "memory_limit = 2G" >> /usr/local/etc/php/conf.d/docker-php-memory-limit.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php-max-execution-time.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/docker-php-upload-max-filesize.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/docker-php-post-max-size.ini

# Configure OPcache with more aggressive settings
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=512" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=64" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=50000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit=1255" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.jit_buffer_size=128M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Add Apache performance tweaks
RUN echo "KeepAlive On" >> /etc/apache2/apache2.conf \
    && echo "KeepAliveTimeout 15" >> /etc/apache2/apache2.conf \
    && echo "MaxKeepAliveRequests 100" >> /etc/apache2/apache2.conf \
    && a2enmod headers expires deflate

# Configure Apache caching
RUN echo '<IfModule mod_headers.c>\n\
    Header set X-XSS-Protection "1; mode=block"\n\
    Header set X-Frame-Options "SAMEORIGIN"\n\
    Header set X-Content-Type-Options "nosniff"\n\
    Header set Cache-Control "max-age=31536000, public"\n\
    <FilesMatch "\.(ico|jpg|jpeg|png|gif|css|js|woff2)$">\n\
        Header set Cache-Control "max-age=31536000, public"\n\
    </FilesMatch>\n\
</IfModule>' > /etc/apache2/conf-available/security-headers.conf
    && a2enconf security-headers

WORKDIR /var/www/html

# Add startup script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

RUN echo "realpath_cache_size = 10M" >> /usr/local/etc/php/conf.d/docker-php-realpath.ini \
    && echo "realpath_cache_ttl = 7200" >> /usr/local/etc/php/conf.d/docker-php-realpath.ini