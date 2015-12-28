<?php
namespace thewulf7\friendloc\components;


use thewulf7\friendloc\services\
{
    AuthService, UserService, LocationService, EmailService
};

/**
 * Class AbstractService
 *
 * @package thewulf7\friendloc\components
 *
 * @method \thewulf7\friendloc\services\AuthService getAuthService()
 * @method \thewulf7\friendloc\services\EmailService getEmailService()
 * @method \thewulf7\friendloc\services\UserService getUserService()
 * @method \thewulf7\friendloc\services\LocationService getLocationService()
 * @method \thewulf7\friendloc\services\MapService getMapService()
 */
abstract class AbstractService
{
    use ApplicationHelper;
}