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

RUN echo "Changing www-data UID to $USER_ID and GID to $GROUP_ID" \
    && if [ -z "$(getent group $GROUP_ID)" ]; then addgroup -g $GROUP_ID www-data; else groupmod -g $GROUP_ID $(getent group $GROUP_ID | cut -d: -f1); fi \
    && usermod -u $USER_ID -g $GROUP_ID www-data

FROM base AS prod
COPY php-prod.ini /usr/local/etc/php/conf.d/php.ini
USER www-data