<?php


namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Annotation\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CacheHeaderListener
{
    const TYPE_PRIVATE = 'private';
    const TYPE_PUBLIC = 'public';

    /**
     * @var array[]
     */
    private $caches;

    /**
     * CacheHeaderListener constructor.
     * @param $caches
     */
    public function __construct($caches)
    {
        $this->caches = $caches;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        /** @var View $view */
        $view = $request->attributes->get('_view');

        if (!$view instanceof View || $view->getCachePolicyName() === null) {
            return;
        }

        $name = $view->getCachePolicyName();
        $policy = $this->getPolicyByName($name);

        if (!$policy) {
            return;
        }

        $config = array(
            'max_age' => $policy['max_age']
        );

        if ($policy['type'] == self::TYPE_PUBLIC) {
            $config['public'] = true;
        } elseif ($policy['type'] == self::TYPE_PRIVATE) {
            $config['private'] = true;
        } else {
            throw new \InvalidArgumentException('Type '.$policy['type'].' not allowed');
        }

        $response->setCache($config);
        $event->setResponse($response);
    }

    /**
     * @param $name
     * @return array|null
     */
    private function getPolicyByName($name)
    {
        if (isset($this->caches[$name])) {
            return $this->caches[$name];
        }
        return null;
    }
}
