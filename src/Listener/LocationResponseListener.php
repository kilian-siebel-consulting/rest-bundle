<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class LocationResponseListener
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ExpressionEvaluator
     */
    protected $evaluator;

    /**
     * CreateViewResponseListener constructor.
     * @param RouterInterface $router
     * @param $evaluator
     */
    public function __construct(RouterInterface $router,ExpressionEvaluator $evaluator)
    {
        $this->router = $router;
        $this->evaluator = $evaluator;
    }


    /**
     * {@inheritdoc}
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        /** @var View $configuration */
        $configuration = $request->attributes->get('_view');

        if(
            !$configuration instanceof View ||
            !$configuration->getLocation()
        ) {
            return;
        }

        $objects = $request->attributes->all();

        $params = $this->prepareRouteParameters($configuration->getLocation()->getParams(), $objects);
        $url = $this->router->generate($configuration->getLocation()->getRoute(), $params);

        $response->headers->add(array('Location' => $url));
        $event->setResponse($response);
    }

    /**
     * @param $params
     * @return mixed
     */
    protected function prepareRouteParameters($params, $data){
        $newParams = $params;
        foreach($params as $key => $val)
        {
            $newParams[$key] = $this->evaluator->evaluate($val, $data);

        }

        return $newParams;
    }
}