<?php

use thewulf7\friendloc\services\AuthService;
use thewulf7\friendloc\services\EmailService;
use thewulf7\friendloc\services\LocationService;
use thewulf7\friendloc\services\MapService;
use thewulf7\friendloc\services\SearchService;
use thewulf7\friendloc\services\UserService;

return [
    'authService'     => \DI\object(AuthService::class),
    'emailService'    => \DI\object(EmailService::class),
    'locationService' => \DI\object(LocationService::class),
    'mapService'      => \DI\object(MapService::class),
    'searchService'   => \DI\object(SearchService::class),
    'userService'     => \DI\object(UserService::class),
];