FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    curl \
  && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    zip \
    opcache \
    pcntl \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]
