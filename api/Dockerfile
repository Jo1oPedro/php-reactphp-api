FROM php:8.2-cli

RUN apt update \
    && apt install -y libpq-dev \
                      zip \
                      unzip \
                      p7zip-full

# instalando extensão mysql para pdo
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install sockets
# Installing extension for Memcached
RUN apt install -y libz-dev libmemcached-dev && pecl install memcached && docker-php-ext-enable memcached

ENV COMPOSER_ALLOW_SUPERUSER=1

# baixar o composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.* ./

RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

COPY public /var/www/html/public
COPY src /var/www/html/src

RUN composer dump-autoload --optimize

CMD ["php", "/var/www/html/public/server.php"]