<?php
namespace Ibrows\RestBundle\Listener\View;

use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use Ibrows\RestBundle\Listener\AbstractViewResponseListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class LocationResponseListener extends AbstractViewResponseListener
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
     * @param $params
     * @param $context
     * @return mixed
     */
    protected function prepareRouteParameters(array $params, array $context){
        $newParams = $params;
        foreach($params as $key => $val){
            $newParams[$key] = $this->evaluator->evaluate($val, $context);
        }

        return $newParams;
    }

    protected function onEvent(View $view, Request $request, Response $response, FilterResponseEvent $event)
    {
        if(!$view->getLocation()) {
            return;
        }

        $objects = $request->attributes->all();

        $params = $this->prepareRouteParameters($view->getLocation()->getParams(), $objects);
        $url = $this->router->generate($view->getLocation()->getRoute(), $params);

        $response->headers->add(array('Location' => $url));
        $event->setResponse($response);
    }
}