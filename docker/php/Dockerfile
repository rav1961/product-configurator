FROM php:8.4-fpm

ARG UID=1000
ARG GID=1000


RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    nano \
    procps \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    bcmath \
    intl \
    zip \
    exif \
    opcache \
    gd

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN groupmod -o -g ${GID} www-data \
    && usermod -o -u ${UID} -g www-data www-data

WORKDIR /var/www/backend

USER www-data

CMD ["php-fpm"]