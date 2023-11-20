#!/bin/bash

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

schedule_run() {
  echo "Running schedule task"
  php artisan schedule:run
  echo "Task completed, shutting down the container"
}

# スケジュールされたタスクの実行
if [ "$RUN_SCHEDULE_TASK" = "true" ] && ([ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "test" ]); then
  cache_config $APP_ENV
  schedule_run
else
  if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "test" ]; then
    cache_config $APP_ENV
    run_migrations $APP_ENV
  fi
  echo "Starting php-fpm."
  php-fpm
fi
