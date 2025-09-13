<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsTask;
use Castor\Context;
use Symfony\Component\Process\Process;

use function Castor\context;
use function Castor\import;
use function Castor\io;
use function Castor\load_dot_env;
use function Castor\log;
use function Castor\notify;
use function Castor\run;
use function database\migrate;
use function docker\build;
use function docker\up;

import(__DIR__.'/.castor');

#[AsTask(description: 'Start the project')]
function start(): void
{
    io()->title('Starting the stack');

    load_dot_env();
    if (file_exists(__DIR__.'/.env.docker')) {
        load_dot_env(__DIR__.'/.env.docker');
    }

    build();
    up();
    install();
    migrate();

    notify('The stack is now up and running.');
    io()->success('The stack is now up and running.');
}

#[AsTask(description: 'Stop the project')]
function stop(): void
{
    run(['docker', 'compose', 'stop']);
}

#[AsTask(description: 'Destroy the project')]
function destroy(): void
{
    run(['docker', 'compose', 'down', '--remove-orphans', '--volumes', '--rmi', 'local', 'commands']);
}

#[AsTask(namespace: 'app', description: 'Installs the application (composer, ...)', aliases: ['install'])]
function install(): void
{
    io()->title('Installing the application');

    run(['docker', 'compose', 'exec', 'php', 'composer', 'install']);
}

#[AsTask(namespace: 'app', description: 'Add a package to the project', aliases: ['composer:require'])]
function composer_require(string $package): void
{
    io()->title('Adding package to the project');

    run(['docker', 'compose', 'exec', 'php', 'composer', 'require', $package]);
}

#[AsTask(namespace: 'app', description: 'Clear the application cache', aliases: ['cache-clear'])]
function cache_clear(): void
{
    io()->title('Clearing the application cache');

    run(['docker', 'compose', 'exec', 'php', 'bin/console', 'cache:clear']);
}

#[AsTask(namespace: 'app', description: 'Enter in php container', aliases: ['enter'])]
function enter(): void
{
    io()->title('Entering in php container');

    run(['docker', 'compose', 'exec', 'php', 'bash']);
}

#[AsTask(namespace: 'app', description: 'Run a symfony command', aliases: ['command'])]
function run_command(string $cmd): void
{
    io()->title('Running the symfony command');

    $command = explode(' ', mb_trim($cmd));

    run(['docker', 'compose', 'exec', 'php', 'bin/console', ...$command], context: context()->withPty());
}
