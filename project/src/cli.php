<?php
/**
 * CLI for application
 *
 * use php src/cli.php
 */
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new \DI\ContainerBuilder();

$containerBuilder->addDefinitions(
    [
        \thewulf7\friendloc\components\config\iConfig::class => \DI\object(\thewulf7\friendloc\components\config\Config::class)->constructor(require(__DIR__ . '/config/main.php')),
        \thewulf7\friendloc\components\Application::class    => \DI\object(\thewulf7\friendloc\components\Application::class),
    ]
);

/** @var \thewulf7\friendloc\components\Application $app */
$app = $containerBuilder->build()->get('\thewulf7\friendloc\components\Application');

$entityManager = $app->getEntityManager();

$helper = ConsoleRunner::createHelperSet($entityManager);

$app = ConsoleRunner::createApplication($helper, [
    // Migrations Commands
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
]);

$app->run();