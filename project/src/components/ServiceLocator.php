<?php

use thewulf7\friendloc\services\AuthService;
use thewulf7\friendloc\services\UserService;

return [
    'AuthService' => \DI\object(AuthService::class),
    'UserService' => \DI\object(UserService::class)
];