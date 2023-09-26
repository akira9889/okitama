#/!bin/bash

if [ "$APP_ENV" = "production" ]; then
  # 本番環境で実行するコマンド
  echo "Set config"
  echo DB_HOST=$DB_HOST >> .env.production &&
  echo DB_DATABASE=$DB_DATABASE >> .env.production &&
  echo DB_USERNAME=$DB_USERNAME >> .env.production &&
  echo DB_PASSWORD=$DB_PASSWORD >> .env.production &&
  echo APP_NAME=$APP_NAME >> .env.production &&
  echo APP_KEY=$APP_KEY >> .env.production &&
  echo APP_URL=$APP_URL >> .env.production &&
  echo SESSION_DRIVER=$SESSION_DRIVER >> .env.production &&
  echo SANCTUM_STATEFUL_DOMAINS=$SANCTUM_STATEFUL_DOMAINS >> .env.production &&
  echo SESSION_DOMAIN=$SESSION_DOMAIN >> .env.production &&
  echo 'Laravel env variables configured'
  echo "Cache config"
  php artisan config:cache --env=production
  echo "Migrate"
  php artisan migrate --force
  php-fpm
elif [ "$APP_ENV" = "test" ]; then
  # テスト環境で実行するコマンド
  echo "Set config"
  echo DB_HOST=$DB_HOST >> .env.test &&
  echo DB_DATABASE=$DB_DATABASE >> .env.test &&
  echo DB_USERNAME=$DB_USERNAME >> .env.test &&
  echo DB_PASSWORD=$DB_PASSWORD >> .env.test &&
  echo APP_NAME=$APP_NAME >> .env.test &&
  echo APP_KEY=$APP_KEY >> .env.test &&
  echo APP_URL=$APP_URL >> .env.test &&
  echo SESSION_DRIVER=$SESSION_DRIVER >> .env.test &&
  echo SANCTUM_STATEFUL_DOMAINS=$SANCTUM_STATEFUL_DOMAINS >> .env.test &&
  echo SESSION_DOMAIN=$SESSION_DOMAIN >> .env.test &&
  echo 'Laravel env variables configured'
  echo "Cache config"
  php artisan config:cache --env=test
  echo "Migrate"
  php artisan migrate --force
  php-fpm
else
  php-fpm
fi
