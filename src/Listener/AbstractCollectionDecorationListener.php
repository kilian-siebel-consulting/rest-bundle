<?php


namespace Ibrows\RestBundle\Listener;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Annotation\View as IbrowsView;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

abstract class AbstractCollectionDecorationListener
{

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if (!$result instanceof CollectionRepresentation) {
            return;
        }

        if (($result instanceof CollectionRepresentation || is_array($event->getControllerResult())) && $this->validateCollection($result)) {
            $this->decorateView($event);
        }
    }

    /**
     * @param $data
     * @return bool isValid
     */
    private function validateCollection($data)
    {
        foreach ($data as $item) {
            if (!$item instanceof ApiListableInterface) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    protected function decorateView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
        $route = $event->getRequest()->attributes->get('_route');

        /** @var ParamFetcherInterface $paramFetcher */
        $paramFetcher = $event->getRequest()->attributes->get('paramFetcher');

        /** @var View $view */
        $view = $event->getRequest()->attributes->get('_view');
        $params = $this->getParams($view, $event->getRequest());

        $this->addListGroup($view);
        $this->decorate($paramFetcher, $route, $params, $response);
        $event->setControllerResult($response);
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param                       $route
     * @param array                 $params
     * @param                       $response
     * @return mixed
     */
    protected abstract function decorate(ParamFetcherInterface $paramFetcher, $route, array $params, & $response);

    /**
     * @param View $view
     */
    private function addListGroup(View $view)
    {
        if (
            $view &&
            count($view->getSerializerGroups()) > 0
        ) {
            $view->setSerializerGroups(
                array_merge(
                    $view->getSerializerGroups(),
                    [
                        'hateoas_list',
                    ]
                )
            );
        }
    }

    /**
     * @param View    $view
     * @param Request $request
     *
     * @return array
     */
    private function getParams(View $view, Request $request)
    {
        $params = [];
        if (
            $view instanceof IbrowsView &&
            is_array($view->getRouteParams())
        ) {
            /** @var IbrowsView $view */
            foreach ($view->getRouteParams() as $paramName) {
                $param = $request->get($paramName);
                if (
                    is_object($param) &&
                    method_exists($param, 'getId')
                ) {
                    $param = $param->getId();
                }
                $params[$paramName] = $param;
            }
        }
        return $params;
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param                       $key
     * @return null
     */
    protected function getParameter(ParamFetcherInterface $paramFetcher, $key)
    {
        $params = $paramFetcher->all();
        if (isset($params[$key])) {
            return $params[$key];
        }
        return null;
    }

    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param                       $key
     * @return null
     */
    protected function hasParameter(ParamFetcherInterface $paramFetcher, $key)
    {
        $params = $paramFetcher->all();

        return array_key_exists($key, $params);
    }
}