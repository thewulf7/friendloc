<?php
namespace thewulf7\friendloc\components\router\urlrules;


/**
 * Class SimpleUrlRule
 *
 * @package thewulf7\friendloc\components\router\urlrules
 */
class SimpleUrlRule implements iUrlRule
{
    /**
     * @var string
     */
    private $_uri;

    /**
     * @var string
     */
    private $_class;

    /**
     * @var string
     */
    private $_route;

    /**
     * @var string
     */
    private $_action;

    /**
     * @var string
     */
    private $_method;

    /**
     * SimpleUrlRule constructor.
     *
     * @param array $arRule
     */
    public function __construct(array $arRule)
    {
        if (!array_key_exists('class', $arRule))
        {
            throw new \InvalidArgumentException('Class declaration is missing in rule');
        }
        $this->_class = $arRule['class'];

        if (!array_key_exists('route', $arRule))
        {
            $class          = explode('\\', $this->getClass());
            $classShortName = array_pop($class);
            $this->_route   = '\/' . lcfirst(str_replace('Controller', '', $classShortName));
        } else
        {
            $this->_route = $arRule['route'] === '/' ? '' : $arRule['route'];
        }
    }

    /**
     * Validate current path
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->getUri())
        {
            throw new \InvalidArgumentException('No uri provided.');
        }

        $pattern = $this->getRoute() . '\/(\S*?)';

        if (preg_match('/^' . $pattern . '$/', $this->getUri(), $action))
        {
            $path          = explode('/', $action[1]);
            $this->_action = array_shift($path);

            return true;
        }

        return false;
    }

    /**
     * Get Uri
     *
     * @return mixed
     */
    public function getUri(): string
    {
        return $this->_uri;
    }

    /**
     * Set uri
     *
     * @param mixed $uri
     *
     * @return SimpleUrlRule
     */
    public function setUri($uri): SimpleUrlRule
    {
        $this->_uri = $uri;

        return $this;
    }

    /**
     * Get Class
     *
     * @return mixed
     */
    public function getClass(): string
    {
        return $this->_class;
    }

    /**
     * Get Route
     *
     * @return mixed
     */
    public function getRoute(): string
    {
        return $this->_route;
    }

    /**
     * Get Action
     *
     * @return mixed
     */
    public function getAction(): string
    {
        return $this->_action;
    }

    /**
     * Get Method
     *
     * @return mixed
     */
    public function getMethod(): string
    {
        return $this->_method;
    }

    /**
     * Set method
     *
     * @param mixed $method
     *
     * @return SimpleUrlRule
     */
    public function setMethod($method): SimpleUrlRule
    {
        $this->_method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getParams(): string
    {
        // TODO: Implement getParams() method.
    }
}