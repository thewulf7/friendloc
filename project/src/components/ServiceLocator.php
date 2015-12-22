<?php

use thewulf7\friendloc\services\AuthService;
use thewulf7\friendloc\services\UserService;

return [
    'authService' => \DI\object(AuthService::class),
    'userService' => \DI\object(UserService::class)
];