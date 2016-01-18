<?php
namespace Ibrows\RestBundle\Listener\Decoration;

use Doctrine\Common\Collections\Collection;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Listener\AbstractCollectionDecorationListener;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class CollectionDecorationListener extends AbstractCollectionDecorationListener
{
    /**
     * @var TransformerInterface
     */
    private $resourceTransformer;

    /**
     * CollectionDecorationListener constructor.
     * @param TransformerInterface $resourceTransformer
     */
    public function __construct(
        TransformerInterface $resourceTransformer
    ) {
        $this->resourceTransformer = $resourceTransformer;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if(
            (
                $result instanceof Collection ||
                is_array($result)
            ) &&
            $this->validateCollection($result)
        ) {

            $this->decorateView($event);
        }
    }

    protected function decorate(ParamFetcherInterface $paramFetcher, $route, array $params, & $response)
    {
        $element = reset($response);
        $resourceConfig = $this->resourceTransformer->getResourceConfig($element);

        $response = new CollectionRepresentation(
            $response,
            $resourceConfig ? $resourceConfig['plural_name'] : null
        );
    }
}