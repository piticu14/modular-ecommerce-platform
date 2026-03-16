FROM php:8.4-fpm

# system deps
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

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# entrypoint
COPY docker/*.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/*.sh

ARG APP_USER=www-data
ARG APP_GROUP=www-data

WORKDIR /var/www/html

COPY . /var/www/html

# Set permissions for Laravel
RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R ${APP_USER}:${APP_GROUP} storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENTRYPOINT ["entrypoint.sh"]
