<?php
namespace thewulf7\friendloc\components\config;


/**
 * Class AbstractConfig
 *
 * @package hellofresh\config
 */
abstract class AbstractConfig
{
    protected $storage;
    /**
     * @param array $storage
     */
    public function __construct(array $storage)
    {
        $this->storage = $storage;
    }
}