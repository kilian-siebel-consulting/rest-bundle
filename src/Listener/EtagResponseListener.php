<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class EtagResponseListener
{
    /**
     * @var boolean
     */
    private $enabled;

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
        $this->enabled = $configuration['enabled'];
        $this->hashingAlgorithm = $configuration['hashing_algorithm'];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (
            $this->enabled &&
            in_array($event->getRequest()->getMethod(), [Request::METHOD_GET, Request::METHOD_HEAD]) &&
            $event->getResponse()->isSuccessful()
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