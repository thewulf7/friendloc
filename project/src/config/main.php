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
    'appName'              => 'FriendLock',
    'controllersNamespace' => 'thewulf7\friendloc\controllers',
    'db'                   => [
        'host'     => 'localhost',
        'dbname'   => 'friendloc',
        'username' => 'friendloc_user',
        'password' => '123456',
        'charset'  => 'UTF-8',
    ],
    'url'                  => [
        '([a-z0-9+_\-]+)/([a-z0-9+_\-]+)/([0-9]+)' => '$controller/$action/$id',
        '([a-z0-9+_\-]+)/([a-z0-9+_\-]+)'          => '$controller/$action',
        '([a-z0-9+_\-]+)/?'                        => '$controller',
    ],
];