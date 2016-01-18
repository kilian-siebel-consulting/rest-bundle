<?php
namespace Ibrows\RestBundle\Listener\Decoration;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Listener\AbstractCollectionDecorationListener;
use Ibrows\RestBundle\Representation\OffsetRepresentation;

class OffsetDecorationListener extends AbstractCollectionDecorationListener
{

    protected function decorate(ParamFetcherInterface $paramFetcher, $route, array $params, & $response)
    {
        if(!$this->hasParameter($paramFetcher, 'limit') || !$this->hasParameter($paramFetcher, 'offset') ){
            return;
        }

        $limit = $this->getParameter($paramFetcher, 'limit');
        $offset = $this->getParameter($paramFetcher, 'offset');

        $response = new OffsetRepresentation(
            $response,
            $route,
            $params,
            $offset,
            $limit
        );
    }
}