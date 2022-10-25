<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/crontab.php';
require 'contrib/webpack_encore.php';
require 'tools/deployer/symfony_secrets.php';

// Config

set('env', [
    'APP_ENV' => 'prod',
]);

set('repository', 'git@github.com:jobbsy-dev/jobbsy.git');

add('shared_files', [
    'config/secrets/prod/prod.decrypt.private.php'
]);
add('shared_dirs', [
    'var/log',
    'var/sessions',
    'public/images',
]);
add('writable_dirs', [
    'var',
    'var/cache',
    'var/log',
    'var/sessions',
]);

set('ssh_port', getenv('SSH_PORT'));
set('ssh_user', getenv('REMOTE_USER'));
set('deploy_path', getenv('DEPLOY_PATH'));

// Hosts

host('jobbsy.dev')
    ->setPort('{{ ssh_port }}')
    ->set('remote_user', '{{ ssh_user }}')
    ->set('deploy_path', '{{ deploy_path }}');

// Hooks

after('deploy:failed', 'deploy:unlock');

after('deploy:update_code', 'yarn:install');
after('deploy:update_code', 'webpack_encore:build');

after('deploy:vendors', 'symfony_secrets:decrypt_to_local');
after('symfony_secrets:decrypt_to_local', 'symfony_secrets:dump_env');
