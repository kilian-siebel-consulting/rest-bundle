<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class IfNonMatchResponseListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $event->getResponse()->isNotModified($event->getRequest());
    }
}
