<?php
/**
 * Main config file
 *
 * friendloc.main.php
 * User: johnnyutkin
 * Date: 18.12.15
 * Time: 11:55
 */

return [
    'appName'           => 'FriendLock',
    'defaultAction'     => 'index',
    'defaultController' => \thewulf7\friendloc\controllers\DefaultController::class,
    'modelsFolder'      => [
        __DIR__ . '/../models',
    ],
    'db'                => [
        'driver'   => 'pdo_pgsql',
        'host'     => 'localhost',
        'user'     => 'friendloc_user',
        'password' => '123456',
        'dbname'   => 'friendloc',
    ],
    'urlRules'          => [
        ['GET', '/', [\thewulf7\friendloc\controllers\DefaultController::class, 'index']],
        ['GET', '/users/login/{action}', [\thewulf7\friendloc\controllers\DefaultController::class, 'index']],
    ],
];