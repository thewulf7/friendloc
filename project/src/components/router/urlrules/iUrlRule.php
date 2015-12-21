<?php
namespace thewulf7\friendloc\components\router\urlrules;


/**
 * Interface iUrlRule
 *
 * @package thewulf7\friendloc\components\router\urlrules
 */
interface iUrlRule
{
    /**
     * @return mixed
     */
    public function validate();

    /**
     * @param $uri
     *
     * @return mixed
     */
    public function setUri($uri);

    /**
     * @return string
     */
    public function getUri(): string;

    /**
     * @return string
     */
    public function getAction(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @param $method
     *
     * @return mixed
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * @return string
     */
    public function getParams(): array;
}