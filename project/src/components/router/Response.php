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
     * @var
     */
    private $_id;

    /**
     * @var
     */
    private $_type;

    /**
     * @var array
     */
    private $_data;

    /**
     * Response constructor.
     *
     * @param int    $id
     * @param string $type
     * @param mixed  $array
     */
    public function __construct(int $id, string $type, $array)
    {
        $this->_id   = $id;
        $this->_type = $type;
        $this->_data = $array;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'         => $this->_id,
            'type'       => $this->_type,
            'properties' => $this->_data,
        ];
    }
}