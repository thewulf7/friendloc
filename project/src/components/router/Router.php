<?php
namespace thewulf7\friendloc\components\router;


use DI\NotFoundException;
use thewulf7\friendloc\components\config\iConfig;

/**
 * Class Router
 *
 * @package thewulf7\friendloc\components
 */
class Router
{
    /**
     * @var string
     */
    private $_controller;

    /**
     * @var string
     */
    private $_action;

    /**
     * @var array
     */
    private $_params;

    /**
     * @param iConfig $config
     * @param Request $request
     *
     * @throws NotFoundException
     * @throws \HttpRequestMethodException
     */
    public function __construct(iConfig $config, Request $request)
    {
        $rules  = $config->get('urlRules');
        $action = null;
        $param  = [];
        $token  = [];

        foreach ($rules as $rule)
        {
            list($method, $uri, $caller) = $rule;

            preg_match('/\{(.*)\}/', $uri, $token);

            $pattern = preg_replace(
                [
                    '/\//',
                    '/\{(.*)\}/',
                ],
                [
                    '\/',
                    '(\w+)',
                ], $uri);

            if(preg_match('/^' . $pattern . '$/', $request->getPath(), $param))
            {
                if($method === $request->getMethod())
                {
                    $action = $caller;
                    break;
                } else {
                    throw new \HttpRequestMethodException('405 Method not Allowed');
                }
            }
        }

        if ($action)
        {
            $act = is_array($action) ? $action[1] : $config->get('defaultAction');
            $this->setAction($act);
            $this->_controller = is_array($action) ? $action[0] : $action;
        } else
        {
            throw new NotFoundException('404 Method not found');
        }

        $paramArray = count($param) > 0 ? [$token[1] => $param[1]] : [];

        $this->_params = array_merge($paramArray, $request->getQuery());
    }

    /**
     * Get Controller
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->_controller;
    }

    /**
     * Get Action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->_action;
    }

    /**
     * Get Params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return Router
     */
    public function setAction($action): router
    {
        $this->_action = $action . 'Action';

        return $this;
    }
}