<?php
namespace thewulf7\friendloc\components;


use DI\Container;
use Doctrine\ORM\EntityManager;
use thewulf7\friendloc\components\config\iConfig;
use thewulf7\friendloc\components\router\Request;
use thewulf7\friendloc\components\router\Router;

/**
 * Class ApplicationHelper
 *
 * @package thewulf7\friendloc\components
 *
 * @method entityManager getEntityManager()
 * @method Request getRequest()
 * @method Router getRouter()
 */
trait ApplicationHelper
{
    /**
     * @var Container
     */
    private $_container;

    /**
     * Controller constructor.
     *
     * @param Container $container
     * @param iConfig   $config
     */
    public function __construct(Container $container, iConfig $config)
    {
        $this->_container = $container;

        $this->init();
    }

    /**
     *  Default function
     */
    public function init()
    {
        $services = require('ServiceLocator.php');

        foreach ($services as $serviceName => $service)
        {
            $this->addToContainer($serviceName, $service);
        }
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
     * @param string $name
     * @param mixed  $object
     *
     * @return mixed
     */
    public function addToContainer(string $name, $object)
    {
        $this->getContainer()->set($name, $object);

        return $this;
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