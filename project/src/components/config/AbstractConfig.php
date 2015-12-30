<?php
namespace thewulf7\friendloc\components\config;


/**
 * Class AbstractConfig
 *
 * @package thewulf7\friendloc\components\config
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