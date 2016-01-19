<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class IfNonMatchResponseListener
{
    /**
     * @var boolean
     */
    private $enabled;

    /**
     * DebugViewResponseListener constructor.
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->enabled = $configuration['enabled'];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (
            $this->enabled &&
            $event->getResponse()->isNotModified($event->getRequest())
        ) {
            $event->getResponse()->setNotModified();
        }
    }
}