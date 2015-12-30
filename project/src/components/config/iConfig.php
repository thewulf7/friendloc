<?php
namespace thewulf7\friendloc\components\config;


/**
 * Interface iConfig
 *
 * @package thewulf7\friendloc\components\config
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