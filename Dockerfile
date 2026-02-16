FROM dunglas/frankenphp:latest-php8.3-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
