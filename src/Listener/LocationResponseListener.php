<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function __construct(RouterInterface $router, ExpressionEvaluator $evaluator)
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
        $view = $request->attributes->get('_view');

        if (!$view instanceof View || !$view->getLocation()) {
            return;
        }

        $objects = $request->attributes->all();

        $params = $this->prepareRouteParameters($view->getLocation()->getParams(), $objects);
        $url = $this->router->generate($view->getLocation()->getRoute(), $params);

        $response->headers->add(array('Location' => $url));
        $event->setResponse($response);
    }


    /**
     * @param $params
     * @param $context
     * @return mixed
     */
    protected function prepareRouteParameters(array $params, array $context)
    {
        $newParams = $params;
        foreach ($params as $key => $val) {
            $newParams[$key] = $this->evaluator->evaluate($val, $context);
        }

        return $newParams;
    }
}
