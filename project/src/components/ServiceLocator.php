<?php

use thewulf7\friendloc\services\AuthService;
use thewulf7\friendloc\services\LocationService;
use thewulf7\friendloc\services\UserService;

return [
    'authService'     => \DI\object(AuthService::class),
    'locationService' => \DI\object(LocationService::class),
    'userService'     => \DI\object(UserService::class),
];