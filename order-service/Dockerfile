FROM php:8.4-fpm

# system deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    netcat-openbsd \
    curl \
  && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    bcmath \
    zip \
    opcache \
    pcntl \
    sockets \
  && pecl install redis \
  && docker-php-ext-enable redis \
  && rm -rf /var/lib/apt/lists/*

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# entrypoint
COPY docker/wait-for.sh /usr/local/bin/wait-for
RUN chmod +x /usr/local/bin/wait-for

COPY order-service/.docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint


WORKDIR /var/www/html

ENTRYPOINT ["entrypoint"]
