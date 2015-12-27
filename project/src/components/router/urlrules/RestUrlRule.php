<?php
namespace thewulf7\friendloc\components\router\urlrules;


/**
 * Class RestUrlRule
 *
 * @package thewulf7\friendloc\components\router\urlrules
 */
class RestUrlRule implements iUrlRule
{
    /**
     *
     */
    const API_VERSION = '/v1/';

    /**
     * @var
     */
    private $_uri;

    /**
     * @var string
     */
    private $_method = 'GET';

    /**
     * @var
     */
    private $_class;

    /**
     * @var string
     */
    private $_route;

    /**
     * @var string
     */
    private $_action;

    /**
     * @var array
     */
    private $_params = [];

    /**
     * @var array
     */
    private $_specialRules = [];

    /**
     * @var bool
     */
    private $_strict = false;

    /**
     * RestUrlRule constructor.
     *
     * @param array $arRule
     */
    public function __construct(array $arRule)
    {
        if (!array_key_exists('class', $arRule))
        {
            throw new \InvalidArgumentException('Class declaration is missing in rule');
        }
        $this->_class = $arRule['class'];

        if (array_key_exists('special', $arRule))
        {
            $this->_specialRules = $arRule['special'];
        }

        $class          = explode('\\', $this->getClass());
        $classShortName = array_pop($class);
        $route          = lcfirst(str_replace('Controller', '', $classShortName));
        $this->_route   = array_key_exists('plularize', $arRule) && $arRule['plularize'] === false ? $route : $route . 's';
        if(array_key_exists('strict', $arRule))
        {
            $this->_strict = $arRule['strict'];
        }
    }

    /**
     * Validate current uri
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->getUri())
        {
            throw new \InvalidArgumentException('No uri provided.');
        }

        $rules = [
            'GET ' . self::API_VERSION . $this->getRoute() . '/{id}'    => 'view',
            'POST ' . self::API_VERSION . $this->getRoute() . '/{id}'   => 'create',
            'PUT ' . self::API_VERSION . $this->getRoute() . '/{id}'    => 'update',
            'DELETE ' . self::API_VERSION . $this->getRoute() . '/{id}' => 'remove',
        ];

        if ($this->isStrict())
        {
            $rules = $this->_specialRules;
        } else
        {
            $rules += $this->_specialRules;
        }

        foreach ($rules as $rule => $action)
        {
            list($method, $uri) = explode(' ', $rule);

            if ($method !== $this->getMethod())
            {
                continue;
            }

            preg_match_all('/\{(\S*?)\}/', $uri, $token);

            $pattern = preg_replace(
                [
                    '/\//',
                    '/\{id\}/',
                    '/\{(.*)\}/',
                ],
                [
                    '\/',
                    '([0-9]+)',
                    '([0-9A-Za-z\_]*?)',
                ], $uri);



            $pattern = str_replace('\\\\','\\',$pattern);

            if (preg_match_all('/^' . $pattern . '\/?$/', $this->getUri(), $param))
            {

                $this->_action = $action;
                if(isset($param[1]))
                {
                    $param = explode('/', $param[1][0]);

                    if (count($param) > 0 && count($token[1]) > 0)
                    {
                        foreach ($token[1] as $key => $tokenName)
                        {
                            $this->_params[$tokenName] = $param[$key];
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Get Uri
     *
     * @return mixed
     */
    public function getUri(): string
    {
        return $this->_uri;
    }

    /**
     * Set uri
     *
     * @param mixed $uri
     *
     * @return RestUrlRule
     */
    public function setUri($uri): RestUrlRule
    {
        $this->_uri = $uri;

        return $this;
    }

    /**
     * Get Method
     *
     * @return mixed
     */
    public function getMethod(): string
    {
        return $this->_method;
    }

    /**
     * Set method
     *
     * @param mixed $method
     *
     * @return RestUrlRule
     */
    public function setMethod($method): RestUrlRule
    {
        $this->_method = $method;

        return $this;
    }

    /**
     * Get Class
     *
     * @return mixed
     */
    public function getClass(): string
    {
        return $this->_class;
    }

    /**
     * Get Route
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->_route;
    }

    /**
     * Get Action
     *
     * @return mixed
     */
    public function getAction(): string
    {
        return $this->_action;
    }

    /**
     * Get Params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->_params;
    }

    /**
     * Get Strict
     *
     * @return boolean
     */
    public function isStrict()
    {
        return $this->_strict;
    }
}