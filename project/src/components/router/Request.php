<?php
namespace thewulf7\friendloc\components\router;


/**
 * Class Request
 *
 * @package thewulf7\friendloc\components\router
 */
class Request
{
    /**
     * @var string
     */
    private $_path;

    /**
     * @var array
     */
    private $_query;

    /**
     * @var string
     */
    private $_method;

    /**
     * @var array
     */
    private $_bodyParams;

    /**
     * @param string $path
     * @param string $query
     * @param string $method
     * @param array  $body
     */
    public function __construct(string $path, string $query = '', string $method = 'GET', array $body = [])
    {
        $this->_path       = $path;
        $this->_method     = $method;
        $this->_bodyParams = $body;

        parse_str($query, $this->_query);
    }

    /**
     * Get Query
     *
     * @return array
     */
    public function getQuery(): array
    {
        return $this->_query;
    }

    /**
     * Get Path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->_method;
    }

    /**
     * Get BodyParams
     *
     * @return mixed
     */
    public function getBodyParams($name = '')
    {
        return $this->_bodyParams[$name] ?? $this->_bodyParams;
    }


}