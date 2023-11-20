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
  echo FILESYSTEM_DISK=$FILESYSTEM_DISK >> $env_file
  echo AWS_DEFAULT_REGION=$AWS_REGION >> $env_file
  echo AWS_BUCKET=$OKITAMABUCKET_NAME >> $env_file
  echo MAIL_MAILER=$MAIL_MAILER >> $env_file
  echo MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS >> $env_file
  echo MAIL_FROM_NAME=$APP_NAME >> $env_file
  echo VITE_GOO_APP_ID=$GOO_APP_ID >> $env_file
  echo 'Laravel env variables configured'
}

build() {
  echo "building"
  if [ "$1" = "production" ]; then
    npm run build:production
  elif [ "$1" = "test" ]; then
    npm run build:test
  fi
  echo "built"
}

set_env_variables $APP_ENV
build $APP_ENV
