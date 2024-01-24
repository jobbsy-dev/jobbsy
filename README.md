# Jobbsy - Job Board for Symfony

![CI/CD](https://github.com/jobbsy-dev/jobbsy/actions/workflows/tests.yaml/badge.svg?branch=main)

Jobbsy is the first and open source job board for [Symfony](https://symfony.com) framework.

Sponsor open source when publishing a job offer is the main goal of Jobbsy 💖

Online and official website is available on [jobbsy.dev](https://jobbsy.dev).

## 📋 Requirements

- PHP 8.3 or higher
- PHP intl extension
- [Composer](https://getcomposer.org)
- [Docker](https://www.docker.com/)
- (optional) [Symfony CLI](https://symfony.com/download) to easily launch project

## 🏗 How to install

First fork the project and clone it:

```bash
git clone git@github.com:<your-fork>/jobbsy.git
cd jobbsy
```

Install PHP dependencies using Composer:

```bash
composer install
```

Launch database service (and adapt it to your needs by copying `docker-compose.override.yaml.dist` to `docker-compose.override.yaml`)

```bash
docker compose up -d
```

Finally, run database migrations:

```bash
php bin/console doctrine:migrations:migrate
```

If you want some data to start you can load fixtures:

```bash
php bin/console doctrine:fixtures:load
```

## 🚀 How to launch

If you are using Symfony CLI simply run:

```bash
cd jobbsy
symfony serve
```

Then access the application in your browser at the given URL (https://localhost:8000 by default) and 🎉

For other cases install and run a web server like Nginx or Apache. You can find some default Symfony configuration in the [docs](https://symfony.com/doc/current/setup/web_server_configuration.html).

## 🧪 Run tests

Init test database and load fixtures:

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console doctrine:fixtures:load --env=test
```

Then run test suite:

```bash
php ./bin/phpunit
```

## Inspirations

- [Larajobs](https://larajobs.com)
- [VueJobs](https://vuejobs.com)
- [GoRails Jobs](https://jobs.gorails.com)
