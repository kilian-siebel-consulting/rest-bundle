<?php
namespace Ibrows\RestBundle\Annotation;

use FOS\RestBundle\Controller\Annotations\View as BaseView;

/**
 * View annotation class.
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
 *
 * @codeCoverageIgnore
 */
class View extends BaseView
{
    /**
     * @var array
     */
    protected $routeParams = [];

    /**
     * @var Route
     * Response Header Field
     */
    protected $location;

    /**
     * @var string
     */
    protected $cachePolicyName;

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * @return Route
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Route $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getCachePolicyName()
    {
        return $this->cachePolicyName;
    }

    /**
     * @param string $cachePolicyName
     */
    public function setCachePolicyName($cachePolicyName)
    {
        $this->cachePolicyName = $cachePolicyName;
    }

}