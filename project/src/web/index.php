<?php

use thewulf7\friendloc\components\Application;
use thewulf7\friendloc\components\config\{iConfig, Config};
use function DI\object;
use function DI\get;

ini_set('display_errors', 0);

require('../../vendor/autoload.php');

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions(
    [
        iConfig::class     => object(Config::class)->constructor(require('../config/main.php')),
        Application::class => object(Application::class),
    ]
);

/** @var Application $app */
$app = $containerBuilder->build()->get('\thewulf7\friendloc\components\Application');
$app
    ->setDevMode(true)
    ->run();