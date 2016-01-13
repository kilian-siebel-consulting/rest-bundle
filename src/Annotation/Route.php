<?php
namespace Ibrows\RestBundle\Annotation;

/**
 * Class Route
 * @package ApiBundle\Annotation
 *
 * @Annotation
 * @Target("ANNOTATION")
 */
class Route
{
    /**
     * @var string
     */
    public $route;

    /**
     * @var array
     */
    public $params;

    /**
     * @var array
     */
    public $parameterNames;

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getParameterNames()
    {
        return $this->parameterNames;
    }

    /**
     * @param array $parameterNames
     */
    public function setParameterNames($parameterNames)
    {
        $this->parameterNames = $parameterNames;
    }
}