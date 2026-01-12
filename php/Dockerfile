FROM php:8.4-fpm-alpine3.21

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk update && apk add vim
RUN apk update && apk add curl
RUN apk update && apk add  zip
RUN apk update && apk add  unzip
RUN apk update && apk add icu-dev git
RUN apk update && apk add npm

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install pdo_mysql
