<?php
/**
 * Created by PhpStorm.
 * User: stefanvetsch
 * Date: 13.01.16
 * Time: 13:58
 */

namespace Ibrows\RestBundle\Listener;


use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ExclusionPolicyResponseListener
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $paramName;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->enabled = $config['enabled'];
        $this->paramName = $config['param_name'];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (
            !$this->enabled ||
            !$event->getRequest()->attributes->has('paramFetcher') ||
            !$event->getRequest()->attributes->has('_view')
        ) {
            return;
        }

        /** @var ParamFetcher $paramFetcher */
        $paramFetcher = $event->getRequest()->attributes->get('paramFetcher');

        /** @var View $view */
        $view = $event->getRequest()->attributes->get('_view');

        try {
            $e = $paramFetcher->get($this->paramName);
        } catch(\InvalidArgumentException $e) {
            // There is no way to check if a param exists without catching an exception
            return;
        }

        $groups = [ $e ];

        if(is_array($origGroups = $view->getSerializerGroups())) {
            $groups = array_merge($origGroups, $groups);
        }

        $view->setSerializerGroups($groups);
    }
}