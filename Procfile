web: heroku-php-nginx -C .dokku/nginx_app.conf public/
release: php bin/console doctrine:migrations:migrate -n
worker: php bin/console messenger:consume async --time-limit=3600
scheduler: php bin/console messenger:consume scheduler_default --time-limit=3600
