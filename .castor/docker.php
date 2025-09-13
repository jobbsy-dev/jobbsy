<?php

/**
 * Source https://github.com/jolicode/docker-starter/blob/main/.castor/docker.php
 */

namespace docker;

use Castor\Attribute\AsContext;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Castor\Context;
use Symfony\Component\Process\Process;

use function Castor\context;
use function Castor\io;
use function Castor\run;
use function Castor\variable;
use function Castor\log;

#[AsTask(description: 'Displays infrastructure logs', aliases: ['logs'])]
function logs(
    ?string $service = null,
): void {
    $command = ['logs', '-f', '--tail', '150'];

    if ($service) {
        $command[] = $service;
    }

    docker_compose($command, c: context()->withTty());
}

#[AsTask(description: 'Lists containers status', aliases: ['ps'])]
function ps(): void
{
    docker_compose(['ps']);
}

#[AsTask(description: 'Builds and starts the infrastructure', aliases: ['up'])]
function up(
    ?string $service = null,
): void {
    if (!$service) {
        io()->title('Starting infrastructure');
    }

    $command = [];
    if (file_exists(__DIR__.'/.env.docker')) {
        $command = ['--env-file', '.env.docker'];
    }

    $command = array_merge($command, ['up', '--detach', '--wait', '--no-build']);

    if ($service) {
        $command[] = $service;
    }

    docker_compose($command);
}

#[AsTask(description: 'Stops the infrastructure', aliases: ['stop'])]
function stop(
    ?string $service = null,
): void {
    if (!$service) {
        io()->title('Stopping infrastructure');
    }

    $command = ['stop'];

    if ($service) {
        $command[] = $service;
    }

    docker_compose($command);
}

#[AsTask(description: 'Opens a shell (bash) into a php container', aliases: ['enter'])]
function enter(): void
{
    $c = context()
        ->withTimeout(null)
        ->withTty()
        ->withEnvironment($_ENV + $_SERVER)
        ->withAllowFailure()
    ;

    $command = [
        'exec',
    ];

    $command[] = 'php';
    $command[] = 'bash';

    docker_compose($command, c: $c);
}

#[AsTask(description: 'Cleans the infrastructure (remove container, volume, networks)', aliases: ['destroy'])]
function destroy(
    #[AsOption(shortcut: 'f', description: 'Force the destruction without confirmation')]
    bool $force = false,
): void {
    io()->title('Destroying infrastructure');

    if (!$force) {
        io()->warning('This will permanently remove all containers, volumes, networks... created for this project.');
        io()->note('You can use the --force option to avoid this confirmation.');
        if (!io()->confirm('Are you sure?', false)) {
            io()->comment('Aborted.');

            return;
        }
    }

    docker_compose(['down', '--remove-orphans', '--volumes', '--rmi=local']);
}

#[AsTask(description: 'Builds the infrastructure', aliases: ['build'])]
function build(
    ?string $service = null,
    bool $noCache = false,
): void {
    io()->title('Building infrastructure');

    $command = [];

    $command = [
        ...$command,
        'build',
        '--build-arg', 'UID=' . variable('user_id'),
        '--build-arg', 'GID=' . variable('group_id'),
    ];

    if ($noCache) {
        $command[] = '--no-cache';
    }

    if ($service) {
        $command[] = $service;
    }

    docker_compose($command);
}

/**
 * @param list<string> $subCommand
 */
function docker_compose(array $subCommand, ?Context $c = null): Process
{
    $c ??= context();

    $c = $c
        ->withTimeout(null);

    $command = [
        'docker',
        'compose',
    ];

    foreach ($c['docker_compose_files'] as $file) {
        $command[] = '-f';
        $command[] = $c['root_dir'].'/'.$file;
    }

    $command = array_merge($command, $subCommand);

    return run($command, context: $c);
}

function docker_compose_run(
    string $runCommand,
    ?Context $c = null,
    string $service = 'php',
    bool $noDeps = true,
    ?string $workDir = null,
    bool $portMapping = false,
    array $environmentVariables = [],
): Process {
    $command = [
        'run',
        '--rm',
    ];

    if ($noDeps) {
        $command[] = '--no-deps';
    }

    if ($portMapping) {
        $command[] = '--service-ports';
    }

    if (null !== $workDir) {
        $command[] = '-w';
        $command[] = $workDir;
    }

    foreach ($environmentVariables as $key => $value) {
        $command[] = '-e';
        $command[] = "$key=$value";
    }

    $command[] = $service;
    $command[] = '/bin/sh';
    $command[] = '-c';
    $command[] = "exec {$runCommand}";

    return docker_compose($command, c: $c);
}

function docker_exit_code(
    string $runCommand,
    ?Context $c = null,
    string $service = 'php',
    bool $noDeps = true,
    ?string $workDir = null,
    array $environmentVariables = [],
): int {
    $c = ($c ?? context())->withAllowFailure();

    $process = docker_compose_run(
        runCommand: $runCommand,
        c: $c,
        service: $service,
        noDeps: $noDeps,
        workDir: $workDir,
        environmentVariables: $environmentVariables,
    );

    return $process->getExitCode() ?? 0;
}

#[AsContext(default: true)]
function create_default_context(): Context
{
    $data = [
        'project_name' => 'jobbsy',
        'php_version' => '8.4',
        'docker_compose_files' => [
            'compose.yaml',
        ],
        'macos' => false,
        'power_shell' => false,
        // check if posix_geteuid is available, if not, use getmyuid (windows)
        'user_id' => function_exists('posix_geteuid') ? posix_geteuid() : getmyuid(),
        'group_id' => function_exists('posix_getegid') ? posix_getegid() : getmygid(),
        'root_dir' => dirname(__DIR__),
    ];

    if (file_exists('compose.override.yaml')) {
        $data['docker_compose_files'][] = 'compose.override.yaml';
    }

    $platform = mb_strtolower(php_uname('s'));
    if (str_contains($platform, 'darwin')) {
        $data['macos'] = true;
    } elseif (in_array($platform, ['win32', 'win64', 'windows nt'], true)) {
        $data['power_shell'] = true;
    }

    if ($data['user_id'] > 256000) {
        $data['user_id'] = 1000;
    }

    if (0 === $data['user_id']) {
        log('Running as root? Fallback to fake user id.', 'warning');
        $data['user_id'] = 1000;
    }

    return new Context(
        data: $data,
        environment: ['COMPOSE_BAKE' => true],
        pty: Process::isPtySupported(),
    );
}
