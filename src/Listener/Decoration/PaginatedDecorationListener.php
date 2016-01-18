<?php


namespace Ibrows\RestBundle\Listener\Decoration;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\PaginatedRepresentation;
use Ibrows\RestBundle\Listener\AbstractCollectionDecorationListener;

class PaginatedDecorationListener extends AbstractCollectionDecorationListener
{

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param $route
     * @param array $params
     * @param $response
     * @return mixed
     */
    protected function decorate(ParamFetcherInterface $paramFetcher, $route, array $params, & $response)
    {
        if(!$this->hasParameter($paramFetcher, 'limit') || !$this->hasParameter($paramFetcher, 'page') ){
            return;
        }

        $limit = $this->getParameter($paramFetcher, 'limit');
        $page = $this->getParameter($paramFetcher, 'page');

        if(($limit === null || $page === null)){
            return;
        }

        $response = new PaginatedRepresentation(
            $response,
            $route,
            $params,
            $page,
            $limit,
            null
        );
    }
}