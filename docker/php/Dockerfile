FROM php:8.5-fpm-alpine

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
    && docker-php-ext-install \
        intl \
        zip \
        pdo \
        pdo_mysql \
    && apk del .build-deps \
    && apk add --no-cache \
        git \
        curl \
        unzip \
        icu-libs \
        libzip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh \
    && apk add --no-cache symfony-cli

WORKDIR /var/www/app

EXPOSE 9000

CMD ["php-fpm"]
