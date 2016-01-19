<?php


namespace Ibrows\RestBundle\Listener;


use Ibrows\RestBundle\Annotation\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

abstract class AbstractViewResponseListener
{
    /**
     * {@inheritdoc}
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        /** @var View $configuration */
        $configuration = $request->attributes->get('_view');

        if(!$configuration instanceof View) {
            return;
        }

        $this->onEvent($configuration, $event->getRequest(), $event->getResponse(), $event);
    }

    abstract protected function onEvent(View $view, Request $request, Response $response, FilterResponseEvent $event);


}