# syntax=docker/dockerfile:1

FROM composer:2 AS composer

FROM mlocati/php-extension-installer:latest AS php-ext-installer

FROM php:8.2-cli AS app

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    mariadb-client-compat \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions using prebuilt installer
COPY --from=php-ext-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql mysqli xdebug

# Configure Xdebug for development
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.log=/var/www/html/logs/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer from prebuilt image
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Copy application files
COPY . .

# Expose port for PHP development server
EXPOSE 8080

# Copy entrypoint script
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

CMD ["/bin/bash", "/docker-entrypoint.sh"]
