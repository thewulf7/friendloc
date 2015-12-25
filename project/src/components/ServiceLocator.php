<?php

use thewulf7\friendloc\services\AuthService;
use thewulf7\friendloc\services\FriendsService;
use thewulf7\friendloc\services\LocationService;
use thewulf7\friendloc\services\UserService;

return [
    'authService'     => \DI\object(AuthService::class),
    'friendsService'  => \DI\object(FriendsService::class),
    'locationService' => \DI\object(LocationService::class),
    'userService'     => \DI\object(UserService::class),
];