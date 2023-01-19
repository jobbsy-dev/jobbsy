<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$application = new Application();
$application->add(new \App\Command\UpdateTagCommand());
$application->setDefaultCommand('app:update:CreateRelationJobTag', true);

$nbrJob = 10;
$output='';
$arguments = array(
    'command' => 'app:update:CreateRelationJobTag',
    '--batch' => $nbrJob,
);
$input = new ArrayInput($arguments);
$returnCode = $application->run($input, $output);