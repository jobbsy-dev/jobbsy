<?php

namespace database;

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;
use function docker\docker_compose_run;

#[AsTask(description: 'Execute migrations', aliases: ['migrate'])]
function migrate(string $env = 'dev'): void
{
    io()->title('Migrating the database schema');

    run(['docker', 'compose', 'exec', 'php', 'bin/console', 'doctrine:database:create', '--if-not-exists', "--env=$env"]);
    run(['docker', 'compose', 'exec', 'php', 'bin/console', 'doctrine:migration:migrate', '-n', "--env=$env"]);
}

#[AsTask(description: 'Reset database', aliases: ['reset'])]
function reset(string $env = 'dev'): void
{
    docker_compose_run("bin/console doctrine:database:drop --force --if-exists --env=$env");
    migrate($env);
}

#[AsTask(description: 'Load fixtures',aliases: ['fixtures'])]
function fixtures(string $env = 'dev', ?array $groups = null): void
{
    $command = ['docker', 'compose', 'exec', 'php', 'bin/console', 'doctrine:fixtures:load', '-n', "--env=$env"];

    if (null !== $groups) {
        foreach ($groups as $group) {
            $command[] = "--group=$group";
        }
    }

    run($command);
}

#[AsTask(description: 'Generate a new migration', aliases: ['migration'])]
function migration(): void
{
    run(['docker', 'compose', 'exec', 'php', 'bin/console', 'doctrine:migrations:diff']);
}

#[AsTask(description: 'Remove a migration', aliases: ['migration-down'])]
function removeMigration(): void
{
    run(['docker', 'compose', 'exec', 'php', 'bin/console', 'doctrine:migrations:migrate', 'prev']);
}
