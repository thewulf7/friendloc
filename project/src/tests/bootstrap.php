<?php
/**
 * friendloc.bootstrap.php
 * User: johnnyutkin
 * Date: 30.12.15
 * Time: 22:46
 */
require_once (__DIR__.'/../../vendor/autoload.php');

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions(
    [
        \thewulf7\friendloc\components\config\iConfig::class => \DI\object(\thewulf7\friendloc\components\config\Config::class)->constructor(require(__DIR__ . '/../config/main.php')),
        \thewulf7\friendloc\components\Application::class    => \DI\object(\thewulf7\friendloc\components\Application::class),
    ]
);
$c = $containerBuilder->build();

/** @var \thewulf7\friendloc\components\Application $app */
$app = $c->get('\thewulf7\friendloc\components\Application');
$app->init();

