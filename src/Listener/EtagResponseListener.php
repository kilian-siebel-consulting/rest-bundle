<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class EtagResponseListener
{
    /**
     * @var string
     */
    private $hashingAlgorithm;

    /**
     * DebugViewResponseListener constructor.
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->hashingAlgorithm = $configuration['hashing_algorithm'];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (in_array($event->getRequest()->getMethod(), [Request::METHOD_GET, Request::METHOD_HEAD]) &&
            $event->getResponse()->isSuccessful() &&
            !$event->getResponse()->headers->hasCacheControlDirective('no-cache')            
        ) {
            $this->setEtag($event);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    protected function setEtag(FilterResponseEvent $event)
    {
        $hash = hash($this->hashingAlgorithm, $event->getResponse()->getContent());
        $event->getResponse()->setEtag($hash);
    }
}
