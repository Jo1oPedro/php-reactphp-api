FROM php:8.2.15-fpm-alpine3.19

# instalando extensão mysql para pdo
RUN docker-php-ext-install pdo pdo_mysql

ENV COMPOSER_ALLOW_SUPERUSER=1

# baixar o composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.* ./

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

COPY public /var/www/html/public
COPY src /var/www/html/src

RUN composer dump-autoload --optimize

CMD ["php", "/var/www/html/public/server.php"]