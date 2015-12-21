<?php
namespace thewulf7\friendloc\components;


use thewulf7\friendloc\services\
{
    AuthService, UserService
};

/**
 * Class AbstractService
 *
 * @package thewulf7\friendloc\components
 *
 * @method AuthService getAuthService()
 * @method UserService getUserService()
 */
abstract class AbstractService
{
    use ApplicationHelper;
}