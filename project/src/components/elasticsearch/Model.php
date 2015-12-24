<?php
namespace thewulf7\friendloc\components\elasticsearch;


/**
 * Interface Model
 *
 * @package thewulf7\friendloc\components\elasticsearch
 */
interface Model
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function setId(int $id);

    /**
     * @return array
     */
    public function toArray(): array;
}