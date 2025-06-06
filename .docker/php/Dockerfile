FROM php:8.4-fpm-alpine AS base

ARG USER_ID
ARG GROUP_ID

WORKDIR /var/www/html

RUN apk add --virtual .build-deps ${PHPIZE_DEPS} \
    && apk add --no-cache \
    bash \
    shadow \
    nano \
    icu-dev \
    zlib-dev \
    icu-dev \
    g++ \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    supervisor \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_pgsql pgsql intl zip opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable pdo_pgsql zip opcache redis \
    && docker-php-source delete \
    && apk del .build-deps $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM base AS dev 
COPY php.ini /usr/local/etc/php/conf.d/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN usermod -u 1000 www-data
USER www-data

FROM base AS prod
COPY php-prod.ini /usr/local/etc/php/conf.d/php.ini
USER www-data