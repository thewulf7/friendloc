<?php
namespace thewulf7\friendloc\components;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use thewulf7\friendloc\components\config\iConfig;

/**
 * Class Application
 *
 * @package thewulf7\friendloc\components
 */
class Application
{
    /**
     * @var iConfig
     */
    private $_config;

    /**
     * @var EntityManager
     */
    private $_entityManager;

    /**
     * @var bool
     */
    private $_devMode = false;

    /**
     * @param iConfig $config
     */
    public function __construct(iConfig $config)
    {
        $this->setConfig($config);

        try
        {
            $configD = Setup::createAnnotationMetadataConfiguration($this->getConfig()->get('modelsFolder'), $this->isDevMode());
            $this->setEntityManager(EntityManager::create($this->getConfig()->get('db'), $configD));
        } catch (\Exception $e)
        {
            echo json_encode(
                [
                    'status' => 'fail',
                    'error'  => $e->getMessage(),
                ]
            );
        }
    }

    public function run(): void
    {
        echo '123';
    }

    /**
     * Get Config
     *
     * @return iConfig
     */
    public function getConfig(): iConfig
    {
        return $this->_config;
    }

    /**
     * Set config
     *
     * @param iConfig $config
     *
     * @return Application
     */
    public function setConfig($config): Application
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * Get EntityManager
     *
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->_entityManager;
    }

    /**
     * Set entityManager
     *
     * @param EntityManager $entityManager
     *
     * @return Application
     */
    public function setEntityManager($entityManager): Application
    {
        $this->_entityManager = $entityManager;

        return $this;
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