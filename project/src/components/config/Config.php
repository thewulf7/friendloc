<?php
namespace thewulf7\friendloc\components\config;


/**
 * Class Config
 *
 * @package hellofresh\config
 */
class Config extends AbstractConfig implements iConfig
{
    /**
     * @param $key
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($key)
    {
        if (!isset($this->storage[$key])) {
            throw new \InvalidArgumentException(sprintf("No data with key '%s' is available.", $key));
        }
        return $this->storage[$key];
    }
}