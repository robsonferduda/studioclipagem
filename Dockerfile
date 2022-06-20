FROM php:7.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev libzip-dev ssl-cert

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-configure zip --with-libzip 

# Install PHP extensions
RUN docker-php-ext-install pgsql pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup Apache2 mod_ssl
RUN a2enmod ssl

# Setup Apache2 HTTPS env
RUN a2ensite default-ssl.conf

RUN a2enmod rewrite

