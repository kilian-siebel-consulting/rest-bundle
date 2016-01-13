<?php
namespace Ibrows\RestBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class DebugViewResponseListener
{
    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
    }
}