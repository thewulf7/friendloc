<?php

use thewulf7\friendloc\services\AuthService;
use thewulf7\friendloc\services\LocationService;
use thewulf7\friendloc\services\MapService;
use thewulf7\friendloc\services\UserService;

return [
    'authService'     => \DI\object(AuthService::class),
    'locationService' => \DI\object(LocationService::class),
    'mapService'      => \DI\object(MapService::class),
    'userService'     => \DI\object(UserService::class),
];