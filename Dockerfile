# syntax=docker/dockerfile:1.7

FROM php:8.5.8-fpm-alpine3.23 AS php-base

RUN apk add --no-cache icu-libs libpq libzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev postgresql-dev \
    && docker-php-ext-install -j$(nproc) bcmath intl pcntl pdo_pgsql zip \
    && apk del .build-deps \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
WORKDIR /var/www/html

FROM php-base AS vendor
COPY --from=composer:2.8.12 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction \
    && php artisan package:discover --ansi

FROM node:24.13.1-alpine3.23 AS frontend
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY --from=vendor /var/www/html/vendor ./vendor
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
ARG VITE_REVERB_APP_KEY=sneakyard-local-key
ARG VITE_REVERB_HOST=localhost
ARG VITE_REVERB_PORT=8081
ARG VITE_REVERB_SCHEME=http
ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY \
    VITE_REVERB_HOST=$VITE_REVERB_HOST \
    VITE_REVERB_PORT=$VITE_REVERB_PORT \
    VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME
RUN npm run build

FROM php-base AS app
COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=frontend --chown=www-data:www-data /var/www/html/public/build /var/www/html/public/build
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage
USER www-data
EXPOSE 9000
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 CMD php-fpm -t || exit 1
CMD ["php-fpm", "-F"]

FROM nginx:1.28.3-alpine3.23 AS web
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=vendor /var/www/html/public /var/www/html/public
COPY --from=frontend /var/www/html/public/build /var/www/html/public/build
RUN mkdir -p /var/www/html/storage/app/public \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R nginx:nginx /var/www/html
EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 CMD wget -qO- http://127.0.0.1:8080/up || exit 1
