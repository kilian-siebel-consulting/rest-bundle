<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class VaryHeaderListener
{
    /**
     * @var string[]
     */
    private $varyHeaders;

    /**
     * CacheHeaderListener constructor.
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->varyHeaders = $configuration['headers'];
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (count($this->varyHeaders) === 0) {
            return;
        }

        $event->getResponse()->setVary(
            array_unique(
                array_merge(
                    $event->getResponse()->getVary(),
                    $this->varyHeaders
                )
            )
        );
    }
}
