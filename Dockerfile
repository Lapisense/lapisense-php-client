# Default version used for quality checks and local development
# CI testing overrides this with build args to test multiple versions
ARG PHP_VERSION=8.5
FROM composer:2 AS composer
FROM php:${PHP_VERSION}-cli-alpine AS base

RUN echo 'memory_limit = 1G' > "$PHP_INI_DIR/conf.d/memory-limit.ini"

# Builder stage: for CI testing (no PCOV, faster builds)
FROM base AS builder
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /app/
COPY composer.json ./
RUN composer install --no-interaction --prefer-dist
COPY . ./

# Develop stage: extends builder with PCOV for coverage
FROM builder AS develop
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && apk del $PHPIZE_DEPS
