<?php
namespace thewulf7\friendloc\components;


use DI\Container;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use thewulf7\friendloc\components\config\iConfig;
use thewulf7\friendloc\components\router\Request;
use thewulf7\friendloc\components\router\Router;
use thewulf7\friendloc\models\User;

use function DI\object;
use function DI\get;

/**
 * Class Application
 *
 * @package thewulf7\friendloc\components
 */
class Application
{

    use ApplicationHelper;

    /**
     * @var bool
     */
    private $_devMode = false;

    public function init()
    {
        $this
            ->addToContainer('entityManager', function (iConfig $config)
            {
                $cache  = new \Doctrine\Common\Cache\ArrayCache();
                $reader = new AnnotationReader();

                $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, $config->get('modelsFolder'));
                $setup = Setup::createAnnotationMetadataConfiguration($config->get('modelsFolder'), $this->isDevMode());
                $setup->setMetadataCacheImpl($cache);
                $setup->setQueryCacheImpl($cache);
                $setup->setMetadataDriverImpl($driver);

                return EntityManager::create($config->get('db'), $setup);
            })
            ->addToContainer('request', function ()
            {
                $urlParts = parse_url($_SERVER['REQUEST_URI']);

                $query = $urlParts['query'] ?? '';

                return new Request($urlParts['path'], $query, $_SERVER['REQUEST_METHOD'], $_POST);
            })
            ->addToContainer('templater', function (iConfig $config)
            {
                $templater = $config->get('templater');

                return $templater['loader']();
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

    /**
     * @throws \DI\NotFoundException
     */
    public function run()
    {
        $router = $this->addToContainer('router', function (Container $c, iConfig $config)
        {
            return new Router($config, $c->get('request'));
        })->getRouter();

        $user = $this->getContainer()->call([$router->getController(), 'beforeAction'], ['method' => $router->getAction()]);

        if ($user)
        {
            if (is_object($user))
            {
                $this->addToContainer('currentUser', $user);
            }

            return $this->getContainer()->call([$router->getController(), $router->getAction()], $router->getParams());
        } else
        {
            return $this->getContainer()->call([$router->getController(), 'redirect'], ['path' => '/auth/login']);
        }
    }

    /**
     * Get DevMode
     *
     * @return bool
     */
    public function isDevMode(): bool
    {
        return $this->_devMode;
    }

    /**
     * Set devMode
     *
     * @param boolean $devMode
     *
     * @return Application
     */
    public function setDevMode($devMode): Application
    {
        $this->_devMode = $devMode;

        return $this;
    }
}