<?php
/**
 * Main config file
 *
 * friendloc.main.php
 * User: johnnyutkin
 * Date: 18.12.15
 * Time: 11:55
 */

use \thewulf7\friendloc\components\router\urlrules\RestUrlRule;
use \thewulf7\friendloc\components\router\UrlRule;

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
    'elastic'           => [
        'connections' => [
            'servers' => [
                'host' => 'localhost',
                'port' => 9200,
            ],
        ],
    ],
    'templater'         => [
        'class'  => Twig_Environment::class,
        'loader' => function ()
        {
            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views');

            return new Twig_Environment($loader);
        },
    ],
    'urlRules'          => [
        [
            'rule'    => UrlRule::REST_RULE,
            'class'   => \thewulf7\friendloc\controllers\UserController::class,
            'special' => [
                'GET ' . RestUrlRule::API_VERSION . 'users/{id}/getFriendList' => 'getFriendsList',
                'PUT ' . RestUrlRule::API_VERSION . 'users/addToFriends'      => 'addToFriends',
                'DELETE ' . RestUrlRule::API_VERSION . 'users/removeFromFriends' => 'removeFromFriends',
            ],
        ],
        [
            'rule'      => UrlRule::REST_RULE,
            'plularize' => false,
            'strict'    => true,
            'class'     => \thewulf7\friendloc\controllers\SearchController::class,
            'special'   => [
                'GET ' . RestUrlRule::API_VERSION . 'search' => 'search',
            ],
        ],
        [
            'rule'  => UrlRule::SIMPLE_RULE,
            'class' => \thewulf7\friendloc\controllers\AuthController::class,
        ],
        [
            'class' => \thewulf7\friendloc\controllers\DefaultController::class,
            'route' => '/',
        ],
    ],
    'emailFrom' => [
        'no-reply@friendloc.dev' => 'Evgenii Utkin'
    ],
];