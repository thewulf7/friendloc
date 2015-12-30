<?php
namespace thewulf7\friendloc\tests;


use DI\Container;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use thewulf7\friendloc\components\config\Config;
use thewulf7\friendloc\components\config\iConfig;
use thewulf7\friendloc\components\ElasticSearch;

abstract class WebTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $_container;

    public function setUp()
    {
        $containerBuilder = new \DI\ContainerBuilder();

        $containerBuilder->addDefinitions(
            [
                iConfig::class => \DI\object(Config::class)->constructor(require(__DIR__ . '/../config/main.php')),
            ]
        );

        $this->_container = $containerBuilder->build();

        $this->init();
    }

    /**
     *  Default function
     */
    public function init()
    {
        $services = require(__DIR__ . '/../components/ServiceLocator.php');

        foreach ($services as $serviceName => $service)
        {
            $this->addToContainer($serviceName, $service);
        }

        $this
            ->addToContainer('entityManager', function (iConfig $config)
            {
                $cache  = new \Doctrine\Common\Cache\ArrayCache();
                $reader = new AnnotationReader();

                $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, $config->get('modelsFolder'));
                $setup  = Setup::createAnnotationMetadataConfiguration($config->get('modelsFolder'), true);
                $setup->setMetadataCacheImpl($cache);
                $setup->setQueryCacheImpl($cache);
                $setup->setMetadataDriverImpl($driver);

                return EntityManager::create($config->get('db'), $setup);
            })
            ->addToContainer('elastic', function (iConfig $config)
            {
                $client = \Elasticsearch\ClientBuilder::create()->build();

                return new ElasticSearch($client, $config->get('modelsFolder'));
            });
        $this->getEntityManager();
        $this->getElastic();
    }

    /**
     * Get Container
     *
     * @return Container
     */
    public function getContainer(): Container
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