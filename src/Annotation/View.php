<?php
namespace Ibrows\RestBundle\Annotation;

use Ibrows\ApiBundle\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View as BaseView;

/**
 * View annotation class.
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
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
     * @return \Ibrows\ApiBundle\Annotation\Route
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param \Ibrows\ApiBundle\Annotation\Route $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
}