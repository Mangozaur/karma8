FROM php:8.1-fpm-alpine

ENV DEBIAN_FRONTEND noninteractive

ENV LIBRARY_PATH=/lib:/usr/lib

RUN set -x \
    && apk update \
    && apk upgrade \
    && apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
    && apk add curl-dev \
    libmemcached-dev \
    unzip \
    libzip-dev \
    zip \
    ca-certificates \
    wget \
    less \
    nano \
    mc \
    sudo \
    git \
    imagemagick \
    imagemagick-dev \
    jpeg-dev \
    libpng-dev \
    libxml2-dev \
    zlib-dev \
    && pecl install apcu-5.1.21 imagick \
    && mkdir /run/php \
    && docker-php-ext-install pdo_mysql exif pcntl bcmath gd xml zip curl \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-enable apcu opcache imagick

RUN echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers.d/www-data

COPY php /

RUN mkdir -p /local/www/  \
    && chown -R www-data:www-data /local/www/

USER www-data
VOLUME /local/www

EXPOSE 80 443 3306

