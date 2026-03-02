FROM php:8.5-cli-alpine

RUN echo 'memory_limit = 1G' > "$PHP_INI_DIR/conf.d/memory-limit.ini"

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
