#!/bin/bash

set_env_variables() {
  local env_file=".env.$1"
  echo "Set config to $env_file"
  echo DB_HOST=$DB_HOST >> $env_file
  echo DB_DATABASE=$DB_DATABASE >> $env_file
  echo DB_USERNAME=$DB_USERNAME >> $env_file
  echo DB_PASSWORD=$DB_PASSWORD >> $env_file
  echo APP_NAME=$APP_NAME >> $env_file
  echo APP_KEY=$APP_KEY >> $env_file
  echo APP_URL=$APP_URL >> $env_file
  echo SESSION_DRIVER=$SESSION_DRIVER >> $env_file
  echo SANCTUM_STATEFUL_DOMAINS=$SANCTUM_STATEFUL_DOMAINS >> $env_file
  echo SESSION_DOMAIN=$SESSION_DOMAIN >> $env_file
  echo 'Laravel env variables configured'
}

cache_config() {
  echo "Cache config"
  php artisan config:cache --env=$1
}

run_migrations() {
  if [ "$1" = "production" ]; then
    echo "Migrate"
    php artisan migrate --force
  elif [ "$1" = "test" ]; then
    echo "Migrate and seed"
    php artisan migrate:refresh --seed --force
  fi
}

if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "test" ]; then
  set_env_variables $APP_ENV
  cache_config $APP_ENV
  run_migrations $APP_ENV
fi

php-fpm
