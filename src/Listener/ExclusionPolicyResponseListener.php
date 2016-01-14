<?php
/**
 * Created by PhpStorm.
 * User: stefanvetsch
 * Date: 13.01.16
 * Time: 13:58
 */

namespace Ibrows\RestBundle\Listener;


use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ExclusionPolicyResponseListener
{
    const PARAM_NAME = '_fieldPolicy';

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param bool $enabled
     */
    public function __construct($enabled = false)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$this->enabled) { 
            return;
        }
        
        /** @var ParamFetcher $paramFetcher */
        $paramFetcher = $event->getRequest()->attributes->get('paramFetcher');
        
        /** @var View $view */
        $view = $event->getRequest()->attributes->get('_view');

        $dynamicQueryParam = new QueryParam();
        $dynamicQueryParam->name = self::PARAM_NAME;
        $dynamicQueryParam->requirements='[a-z0-9_\-]+';
        $paramFetcher->addParam($dynamicQueryParam);
        
        $e = $paramFetcher->get(self::PARAM_NAME);

        if (!$e) {
            return;
        }
        
        $groups = [ $e ];
        
        if(is_array($origGroups = $view->getSerializerGroups())) {
            $groups = array_merge($origGroups, $groups); 
        }
        
        $view->setSerializerGroups($groups);
    }
}