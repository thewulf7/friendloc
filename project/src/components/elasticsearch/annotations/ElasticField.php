<?php
namespace thewulf7\friendloc\components\elasticsearch\annotations;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class ElasticField
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean
     */
    public $includeInAll = true;
}