FROM php:8.1.0-fpm as base

# パッケージ一覧を更新
RUN apt update && \
    # zlibはデータの圧縮に関するアルゴリズムをライブラリ化したもの
    # MariaDBデータベースのクライアントアプリケーションである「mysql」を使えるように「mariadb-client」をインストール
    # libzip-devはzip圧縮時に必要となるライブラリ(libzip は、zlib を使用する)
    apt install -y mariadb-client zlib1g-dev libzip-dev libpng-dev libjpeg62-turbo-dev unzip && \
    # docker-php-ext-installはPHPに標準で備わっている拡張パッケージのインストール＆有効化ができるコマンド
    # PDO_MYSQL は、PHP から MySQL データベースへのアクセスを可能にするための PHP Data Objects (PDO) インターフェイス を実装したドライバ。
    docker-php-ext-install zip pdo_mysql

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.jsとnpmのインストールを行う共通のベースイメージ
FROM base AS node_php_base
RUN apt install -y npm && \
    npm install n -g && \
    n stable

WORKDIR /var/www/html

FROM node_php_base AS build
COPY ./src/ .
RUN  npm install
RUN  npm run build

# 開発環境
FROM node_php_base as dev

COPY ./src/ .
RUN  composer install
RUN  npm install

COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD [ "/start.sh" ]

# 本番環境
FROM base AS production

COPY ./src/ .
COPY --from=build /var/www/html/public/build /var/www/html/public/build

COPY ./docker/start.sh /start.sh
RUN chmod +x /start.sh

RUN composer install --no-dev --optimize-autoloader && \
    php artisan cache:clear && \
    php artisan config:clear && \
    chmod -R 777 storage

VOLUME /var/www/html/public

CMD [ "/start.sh" ]
