<?php

namespace worker;

use Castor\Attribute\AsTask;

use function docker\docker_compose_run;
use function Castor\run;
use function Castor\io;

#[AsTask(description: 'Start consuming messages from transport', aliases: ['worker'])]
function consume(string $transport): void
{
    docker_compose_run('php bin/console messenger:consume ' . $transport . ' -vv');
}

#[AsTask(namespace: 'app', description: 'Stop the worker', aliases: ['messenger:stop'])]
function stop_workers(): void
{
    io()->title('Stopping the worker');

    run(['docker', 'compose', 'exec', 'php', 'bin/console', 'messenger:stop-workers']);
}
