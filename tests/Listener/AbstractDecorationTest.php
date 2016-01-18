<?php


namespace Ibrows\RestBundle\Tests\Listener;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Listener\Decoration\CollectionDecorationListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class AbstractDecorationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $kernel;

    public function setUp()
    {
        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
    }

    protected function setupDecoration($class, $event){
        $collectionListener = new CollectionDecorationListener();
        $collectionListener->onKernelView($event);
        $listener = new $class;
        return $listener;
    }

    protected function getEvent(ParamFetcherInterface $paramFetcher = null, $controllerResult = array(), $view = null, $request = null){
        if($view == null) {
            $view = new View(array());
        }

        if($request == null) {
            $request = new Request(array(), array(), array('_view' => $view, 'paramFetcher' => $paramFetcher, '_route' => 'foo'));
        }

        return new GetResponseForControllerResultEvent($this->kernel,  $request, HttpKernelInterface::MASTER_REQUEST, $controllerResult);
    }

}