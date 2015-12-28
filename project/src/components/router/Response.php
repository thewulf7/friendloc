<?php
namespace thewulf7\friendloc\components\router;


/**
 * Class Response
 *
 * @package thewulf7\friendloc\components\router
 */
class Response implements \JsonSerializable
{
    /**
     * @var array
     */
    private $_data;

    /**
     * Response constructor.
     *
     * @param mixed  $array
     */
    public function __construct($array)
    {
        $this->_data = $array;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->_data;
    }
}