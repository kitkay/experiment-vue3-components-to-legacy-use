FROM ghcr.io/roadrunner-server/roadrunner:2025.1.14 AS roadrunner

FROM php:8.4-cli-alpine3.21

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN apk add --no-cache \
        git \
        unzip \
        vim \
        linux-headers \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        intl \
        mbstring \
        opcache \
        pcntl \
        sockets \
        zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app

CMD ["rr", "serve", "-c", "./roadrunner_conf/.rr.yml"]