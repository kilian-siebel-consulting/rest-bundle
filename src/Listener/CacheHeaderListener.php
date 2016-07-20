<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Annotation\View;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CacheHeaderListener
{
    const TYPE_PRIVATE = 'private';
    const TYPE_PUBLIC = 'public';
    const TYPE_NO_CACHE = 'no-cache';

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
     * @throws InvalidArgumentException
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        /** @var View $view */
        $view = $request->attributes->get('_template');

        if (!$view instanceof View || $view->getCachePolicyName() === null) {
            return;
        }

        $name = $view->getCachePolicyName();
        $policy = $this->getPolicyByName($name);

        if (!$policy) {
            return;
        }

        switch ($policy['type']) {
            case self::TYPE_NO_CACHE:
                break;
            case self::TYPE_PUBLIC:
                $response->setSharedMaxAge($policy['max_age']);

                break;
            case self::TYPE_PRIVATE:
                $response->setMaxAge($policy['max_age']);

                break;
            default:
                throw new InvalidArgumentException('Type ' . $policy['type'] . ' not allowed');
        }
    }

    /**
     * @param $name
     * @return array|null
     */
    private function getPolicyByName($name)
    {
        if (array_key_exists($name, $this->caches)) {
            return $this->caches[$name];
        }
        return null;
    }
}
