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
        $urlFactory = new UrlRule();

        $rules  = $config->get('urlRules');
        $route  = null;

        $this->_params = $request->getQuery();

        foreach ($rules as $rule)
        {
            $oRule = $urlFactory->create($rule);

            $oRule->setUri($request->getPath())->setMethod($request->getMethod());

            if ($oRule->validate())
            {
                $route = $oRule;
                break;
            }
        }

        if ($route)
        {
            $act = $route->getAction() === '' ? $config->get('defaultAction') : $route->getAction();
            $this->setAction($act);
            $this->setController($route->getClass());
            if(count($route->getParams())>0)
            {
                $this->_params = array_merge($route->getParams(),$this->_params);
            }
        } else
        {
            throw new NotFoundException('404 Method not found');
        }
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
     * Set controller
     *
     * @param string $controller
     *
     * @return Router
     */
    public function setController(string $controller): Router
    {
        $this->_controller = $controller;

        return $this;
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
    public function setAction(string $action): router
    {
        $this->_action = $action . 'Action';

        return $this;
    }
}