<?php
namespace thewulf7\friendloc\components\config;


/**
 * Interface iConfig
 *
 * @package hellofresh\config
 */
interface iConfig
{
    /**
     * @param $key
     *
     * @return mixed
     */
    public function get(string $key);
}