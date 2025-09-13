<?php

namespace helper;

use function Castor\capture;
use function Castor\context;
use function Castor\exit_code;

function command_exists(string $command): bool
{
    return !exit_code("which $command", context: context()->withQuiet());
}

function uid(): string
{
    return capture('id -u');
}

function gid(): string
{
    return capture('id -g');
}
