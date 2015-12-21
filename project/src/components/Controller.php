<?php
namespace thewulf7\friendloc\components;


use thewulf7\friendloc\services\
{
    AuthService, UserService
};

/**
 * Class Controller
 *
 * @package thewulf7\friendloc\components
 * @method AuthService getAuthService()
 * @method UserService getUserService()
 */
abstract class Controller
{
    use ApplicationHelper;

    /**
     * @param string $path
     */
    public function redirect($path = '')
    {

    }

    /**
     * @param string $view
     * @param array  $params
     *
     * @return bool
     */
    public function render($view = '', $params = [])
    {
        echo 'render';
        return true;
    }
}