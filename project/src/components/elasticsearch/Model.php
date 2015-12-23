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
     * @return int
     */
    public function getId(): int;

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