FROM php:8.4-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    curl \
    netcat-openbsd \
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

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# entrypoint
COPY docker/wait-for.sh /usr/local/bin/wait-for
RUN chmod +x /usr/local/bin/wait-for

COPY auth-service/.docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html

ENTRYPOINT ["entrypoint"]
