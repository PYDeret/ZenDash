FROM dunglas/frankenphp:latest-php8.3-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo_pgsql intl zip bcmath

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
