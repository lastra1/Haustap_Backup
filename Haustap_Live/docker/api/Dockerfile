FROM php:8.3-cli

# Install system dependencies and PHP extensions needed by Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libonig-dev \
        libzip-dev \
        curl \
    && docker-php-ext-install mbstring pdo_mysql zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Enable Opcache with sensible defaults (including CLI)
RUN printf "opcache.enable=1\nopcache.enable_cli=1\nopcache.memory_consumption=128\nopcache.interned_strings_buffer=8\nopcache.max_accelerated_files=10000\nopcache.validate_timestamps=1\nopcache.revalidate_freq=2\n" > /usr/local/etc/php/conf.d/opcache.ini

# Composer settings for performance and reliability
ENV COMPOSER_CACHE_DIR=/root/.composer/cache
ENV COMPOSER_MEMORY_LIMIT=-1

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Default command launches Laravel dev server
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8001"]