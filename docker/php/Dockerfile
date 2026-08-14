# ---- Stage 1: Composer dependencies ----
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# ---- Stage 2: Runtime image ----
FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache \
    bash \
    mysql-client \
    libzip-dev \
    zip \
    unzip \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    oniguruma-dev \
    icu-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
        intl \
        bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Bring in the already-installed vendor/ folder from stage 1
COPY --from=vendor /app/vendor ./vendor

# Now copy your actual application code (everything you wrote: app/,
# routes/, database/, etc. — this is YOUR code, committed in git)
COPY . .

RUN composer dump-autoload --optimize

# Non-root user — security best practice, never run the app as root
RUN addgroup -g 1000 tazcara && adduser -G tazcara -g tazcara -s /bin/sh -D tazcara \
    && chown -R tazcara:tazcara /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER tazcara

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
