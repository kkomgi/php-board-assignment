FROM php:8.2-cli

# system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip default-mysql-client \
    && docker-php-ext-install pdo_mysql

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
