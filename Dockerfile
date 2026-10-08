# syntax=docker/dockerfile:1

FROM php:8.4-fpm-alpine AS php-base
RUN apk add --no-cache icu-libs libzip libpng libjpeg-turbo freetype \
    && apk add --no-cache --virtual .php-build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl opcache pdo_mysql zip \
    && apk del .php-build-deps
COPY docker/php-production.ini /usr/local/etc/php/conf.d/production.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-production.conf

FROM php-base AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader
COPY app ./app
COPY database ./database
RUN composer dump-autoload --no-dev --optimize --no-scripts

FROM node:22-alpine AS assets
WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY --from=vendor /var/www/html/vendor ./vendor
COPY app ./app
COPY resources ./resources
COPY public ./public
COPY tokens.css vite.config.js ./
RUN npm run build

FROM php-base AS app
WORKDIR /var/www/html
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY resources ./resources
COPY routes ./routes
COPY public ./public
COPY artisan composer.json ./
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=assets /var/www/html/public/build ./public/build
COPY docker/app-entrypoint.sh /usr/local/bin/app-entrypoint
RUN mkdir -p storage/app/private storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod 755 /usr/local/bin/app-entrypoint \
    && php artisan package:discover --ansi
USER www-data
EXPOSE 9000
ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm", "-F"]

FROM nginxinc/nginx-unprivileged:stable-alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
EXPOSE 8181
