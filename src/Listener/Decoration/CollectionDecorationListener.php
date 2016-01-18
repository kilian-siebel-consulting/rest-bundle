<?php
namespace Ibrows\RestBundle\Listener\Decoration;

use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Listener\AbstractCollectionDecorationListener;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class CollectionDecorationListener extends AbstractCollectionDecorationListener
{

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if($result instanceof Collection || is_array($result)) {

            $this->decorateView($event);
        }
    }

    protected function decorate(ParamFetcherInterface $paramFetcher, $route, array $params, & $response)
    {
        $response = new CollectionRepresentation($response);
    }
}