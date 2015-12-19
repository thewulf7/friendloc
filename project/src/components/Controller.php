<?php
namespace thewulf7\friendloc\components;


use DI\Container;

/**
 * Class Controller
 *
 * @package thewulf7\friendloc\components
 */
abstract class Controller
{

    /**
     * @var Container
     */
    private $_container;

    /**
     * Controller constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->_container = $container;
    }

    /**
     * Get Container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * @param $name
     * @param $args
     *
     * @return mixed
     * @throws \DI\NotFoundException
     */
    public function __call($name, $args)
    {
        $methodName = lcfirst(substr($name, 3));
        if ($this->getContainer()->has($methodName))
        {
            return $this->getContainer()->get($methodName);
        } else
        {
            throw new \RuntimeException("Method `{$name}` doesn't exists.");
        }
    }
}