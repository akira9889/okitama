FROM php:8.1.0-fpm as base

# パッケージ一覧を更新
RUN apt update && \
  apt install -y mariadb-client zlib1g-dev libzip-dev unzip && \
  docker-php-ext-install pdo_mysql opcache

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# テスト環境
FROM base AS test

RUN apt update && \
  apt install -y vim

COPY ./src/ .

COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

RUN composer install && \
  php artisan cache:clear && \
  php artisan config:clear && \
  chmod -R 777 storage

COPY ./docker/php/php.production.ini /usr/local/etc/php/php.ini

CMD [ "/start.sh" ]

# 本番環境
FROM base AS production

COPY ./src/ .

COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

RUN composer install --no-dev --optimize-autoloader && \
  php artisan cache:clear && \
  php artisan config:clear && \
  chmod -R 777 storage

COPY ./docker/php/php.production.ini /usr/local/etc/php/php.ini

CMD [ "/start.sh" ]
