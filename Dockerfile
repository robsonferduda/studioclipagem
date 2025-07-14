FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev libzip-dev ssl-cert python3 python3-pip python3-venv

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-configure zip 

# Install PHP extensions
RUN docker-php-ext-install pgsql pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup Apache2 mod_ssl
RUN a2enmod ssl

# Setup Apache2 HTTPS env
RUN a2ensite default-ssl.conf

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod headers

# Copy custom Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Create symbolic link for python
RUN ln -s /usr/bin/python3 /usr/bin/python

# Install Python dependencies
COPY python/relatorios/requirements.txt /tmp/requirements.txt
RUN pip3 install -r /tmp/requirements.txt

# Create output directory for reports
RUN mkdir -p /var/www/html/python/relatorios/output
RUN chown -R www-data:www-data /var/www/html/python/relatorios/output

# Create storage directories and link
RUN mkdir -p /var/www/html/storage/app/public/relatorios
RUN chown -R www-data:www-data /var/www/html/storage

# Copy and setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

