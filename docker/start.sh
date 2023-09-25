#/!bin/bash

if [ "$APP_ENV" = "production" ]; then
  # 本番環境で実行するコマンド
  php artisan config:cache --env=production
  php artisan migrate --force
  php-fpm
elif [ "$APP_ENV" = "test" ]; then
  # テスト環境で実行するコマンド
  php artisan config:cache --env=test
  php artisan migrate --force
  php-fpm
else
  # 本番環境、テスト環境以外で実行するコマンド
  echo DB_HOST=$DB_HOST >> .env &&
  echo DB_DATABASE=$DB_DATABASE >> .env &&
  echo DB_USERNAME=$DB_USERNAME >> .env &&
  echo DB_PASSWORD=$DB_PASSWORD >> .env &&
  echo APP_NAME=$APP_NAME >> .env &&
  echo APP_KEY=$APP_KEY >> .env &&
  echo APP_URL=$APP_URL >> .env &&
  echo SESSION_DRIVER=$SESSION_DRIVER >> .env &&
  echo SANCTUM_STATEFUL_DOMAINS=$SANCTUM_STATEFUL_DOMAINS >> .env &&
  echo SESSION_DOMAIN=$SESSION_DOMAIN >> .env &&
  echo 'Laravel env variables configured'
  php-fpm
fi
