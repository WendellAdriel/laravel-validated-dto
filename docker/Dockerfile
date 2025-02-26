FROM php:8.2-cli-alpine

# INSTALL AND UPDATE COMPOSER
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer self-update

# Install system dependencies
RUN apk add --no-cache --update \
    curl \
    openssl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    unzip

# Install the GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd

WORKDIR /usr/src/app
COPY . .

# INSTALL YOUR DEPENDENCIES
RUN composer install --prefer-dist
