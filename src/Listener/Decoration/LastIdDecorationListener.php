<?php


namespace Ibrows\RestBundle\Listener\Decoration;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Listener\AbstractCollectionDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\LastIdRepresentation;

class LastIdDecorationListener extends AbstractCollectionDecorationListener
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
        if(!$this->hasParameter($paramFetcher, 'limit') || !$this->hasParameter($paramFetcher, 'offsetId') ){
            return;
        }

        $limit = $this->getParameter($paramFetcher, 'limit');
        $last = $this->getParameter($paramFetcher, 'offsetId');
        $sortBy = $this->getParameter($paramFetcher, 'sortBy');
        $sortDir = $this->getParameter($paramFetcher, 'sortDir');

        $resources = $response->getResources();
        $lastElement = end($resources);

        if(!$lastElement || !$lastElement instanceof ApiListableInterface) {
            return;
        }

        $response = new LastIdRepresentation(
            $response,
            $route,
            $params,
            $lastElement->getId(),
            'offsetId',
            $limit,
            'limit',
            $sortBy,
            $sortDir
        );
    }
}