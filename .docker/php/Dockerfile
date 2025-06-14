FROM php:8.4-fpm-alpine AS base

ARG USER_ID
ARG GROUP_ID

WORKDIR /var/www/html

RUN apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} \
    && apk add --no-cache \
    bash \
    shadow \
    nano \
    icu-dev \
    zlib-dev \
    g++ \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    rabbitmq-c-dev \
    supervisor \
    && docker-php-ext-configure intl \
    && pecl install redis \
    && pecl install amqp \
    && docker-php-ext-enable redis amqp \
    && docker-php-ext-install pdo_pgsql pgsql intl zip opcache \
    && docker-php-ext-enable pdo_pgsql zip opcache \
    && docker-php-source delete \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

FROM base AS dev
COPY php.ini /usr/local/etc/php/conf.d/php.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN usermod -u 1000 www-data
USER www-data

FROM base AS prod
COPY php-prod.ini /usr/local/etc/php/conf.d/php.ini
USER www-data