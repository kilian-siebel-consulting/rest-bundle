<?php


namespace Ibrows\RestBundle\Listener\View;


use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Cache\CachePolicy;
use Ibrows\RestBundle\Listener\AbstractViewResponseListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CacheHeaderListener extends AbstractViewResponseListener
{

    /**
     * @var CachePolicy[]
     */
    private $caches;

    /**
     * CacheHeaderListener constructor.
     * @param $caches
     */
    public function __construct($caches)
    {
        $this->caches = array();

        foreach($caches as $cache)
        {
            $name = $cache['name'];
            $c = new CachePolicy($name, $cache['timetolife'], $cache['type']);
            $this->caches[$name] = $c;
        }
    }

    /**
     * @param $name
     * @return CachePolicy|null
     */
    private function getPolicyByName($name){
        if(isset($this->caches[$name])){
            return $this->caches[$name];
        }
        return null;
    }

    /**
     * @param View $view
     * @param Request $request
     * @param Response $response
     * @param FilterResponseEvent $event
     */
    protected function onEvent(View $view, Request $request, Response $response, FilterResponseEvent $event)
    {
        if($view->getCachePolicyName() === null){
            return;
        }

        $name = $view->getCachePolicyName();
        $policy = $this->getPolicyByName($name);

        if(!$policy){
            return;
        }

        $cacheControl = null;
        if($policy->getType() === CachePolicy::TYPE_NO_CACHE){
            $cacheControl = "no-cache";
        }elseif($policy->getType() === CachePolicy::TYPE_NO_STORE){
            $cacheControl = "no-store";
        }elseif($policy->getType() === CachePolicy::TYPE_PRIVATE){
            $cacheControl = 'private, max-age='.$policy->getTimeToLife();
        }elseif($policy->getType() === CachePolicy::TYPE_PUBLIC){
            $cacheControl = 'public, max-age='.$policy->getTimeToLife();
        }else{
            throw new \InvalidArgumentException("CachePolicy type ".$policy->getType()." is not supported");
        }

        $response->headers->add(array('Cache-Control' => $cacheControl));
        $event->setResponse($response);
    }
}