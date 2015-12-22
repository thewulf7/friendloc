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
    'templater'         => [
        'class'  => Twig_Environment::class,
        'loader' => function(){
            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views');
            return new Twig_Environment($loader);
        },
    ],
    'urlRules'          => [
//        [
//            'rule'    => \thewulf7\friendloc\components\router\UrlRule::REST_RULE,
//            'class'   => \thewulf7\friendloc\controllers\UserController::class,
//        ],
        [
            'rule'  => \thewulf7\friendloc\components\router\UrlRule::SIMPLE_RULE,
            'class' => \thewulf7\friendloc\controllers\AuthController::class,
        ],
        [
            'class' => \thewulf7\friendloc\controllers\DefaultController::class,
            'route' => '/',
        ],
    ],
];