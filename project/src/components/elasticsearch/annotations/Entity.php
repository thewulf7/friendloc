<?php
namespace thewulf7\friendloc\components\elasticsearch\annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Entity
{
    /**
     * @var string
     */
    public $index;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $number_of_shards = 1;

    /**
     * @var int
     */
    public $number_of_replicas = 1;
}
