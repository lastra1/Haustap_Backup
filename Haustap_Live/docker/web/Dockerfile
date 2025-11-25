FROM php:8.3-apache

# Enable Apache modules commonly needed for PHP apps
RUN a2enmod rewrite headers

# Use our vhost that points DocumentRoot to /var/www/public
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Useful PHP extensions required by the app
RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
        curl \
    && docker-php-ext-install mbstring pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/public

# Default CMD runs Apache in the foreground
CMD ["apache2-foreground"]
