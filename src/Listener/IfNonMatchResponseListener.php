<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class IfNonMatchResponseListener
{

    /**
     * DebugViewResponseListener constructor.
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getResponse()->isNotModified($event->getRequest())) {
            $event->getResponse()->setNotModified();
        }
    }
}