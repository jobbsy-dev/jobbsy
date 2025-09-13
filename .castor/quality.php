<?php

namespace quality;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function Castor\variable;
use function database\fixtures;
use function database\reset as resetDatabase;
use function docker\docker_compose;
use function docker\docker_compose_run;
use function docker\docker_exit_code;

#[AsTask(description: 'Runs all QA tasks', aliases: ['quality'])]
function all(): int
{
    install();

    $audit = audit();
    $rector = rector();
    $cs = cs();
    $lint = lint();
    $phpstan = phpstan();
    $phpunit = phpunit();

    return max($cs, $phpstan, $phpunit, $lint, $rector, $audit);
}

#[AsTask(description: 'Installs tooling')]
function install(): void
{
    io()->title('Installing tools dependencies');

    docker_compose_run('composer install', workDir: '/app/tools/php-cs-fixer');
    docker_compose_run('composer install', workDir: '/app/tools/rector');
}

#[AsTask(description: 'Run PHP CS Fixer', aliases: ['cs'])]
function cs(bool $check = false): int
{
    if (false === is_dir(variable('root_dir').'/tools/php-cs-fixer/vendor')) {
        io()->error('PHP-CS-Fixer is not installed. Run `castor quality:install` first.');

        return 1;
    }

    if ($check) {
        return docker_exit_code('/app/tools/bin/php-cs-fixer check --diff');
    }

    return docker_exit_code('/app/tools/bin/php-cs-fixer fix');
}

#[AsTask(description: 'Run PHPStan', aliases: ['phpstan'])]
function phpstan(
    #[AsOption(shortcut: 'b', description: 'Generate a baseline file')]
    bool $generateBaseline = false
): int
{
    $c = context()->withAllowFailure();

    $command = [
        'exec',
    ];

    $command[] = 'php';
    $command[] = '/app/vendor/bin/phpstan';

    if ($generateBaseline) {
        $command[] = '-b';
    }

    $process = docker_compose($command, c: $c);

    return $process->getExitCode() ?? 0;
}

#[AsTask(description: 'Composer audit', aliases: ['audit'])]
function audit(): int
{
    return docker_exit_code('composer audit');
}

#[AsTask(description: 'Run phpunit test suite', aliases: ['test', 'phpunit'])]
function phpunit(bool $withCoverage = false): int
{
    resetDatabase('test');
    fixtures(env: 'test');

    $c = context()->withAllowFailure();

    $command = [
        'exec',
    ];

    $command[] = 'php';
    $command[] = '/app/vendor/bin/phpunit';

    $process = docker_compose($command, c: $c);

    return $process->getExitCode() ?? 0;
}

#[AsTask(description: 'Run Rector', aliases: ['rector'])]
function rector(bool $dryRun = false): int
{
    if (false === is_dir(variable('root_dir').'/tools/rector/vendor')) {
        io()->error('Rector is not installed. Run `castor quality:install` first.');

        return 1;
    }

    if ($dryRun) {
        return docker_exit_code('/app/tools/bin/rector --dry-run');
    }

    return docker_exit_code('/app/tools/bin/rector');
}

#[AsTask(description: 'Run various linters', aliases: ['lint'])]
function lint(): int
{
    $lintTwig = docker_exit_code('bin/console lint:twig templates --env=prod');
    $lintContainer = docker_exit_code('bin/console lint:container --no-debug');
    $lintDoctrineSchema = docker_exit_code('bin/console doctrine:schema:validate --skip-sync -vvv --no-interaction');

    return max($lintTwig, $lintContainer, $lintDoctrineSchema);
}
