<?php
/**
 * Created by PhpStorm.
 * User: stefanvetsch
 * Date: 15.01.16
 * Time: 11:02
 */

namespace Ibrows\RestBundle\Tests\Unit\Listener;

use Ibrows\RestBundle\Annotation\Route;
use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use Ibrows\RestBundle\Listener\LocationResponseListener;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class LocationResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;
    private $context;
    
    private $router;

    /**
     * @var ExpressionEvaluator|PHPUnit_Framework_MockObject_MockObject
     */
    private $evaluator;
    
    public function setUp()
    {
        $this->router = self::createMock(Router::class);
        
        $this->evaluator = self::createMock(ExpressionEvaluator::class);
        
        $this->kernel = self::createMock(HttpKernelInterface::class);
        $this->context = self::createMock(RequestContext::class);
    }
    
    public function testInvalidViewAttribute()
    {
        $listener = $this->getListener();

        $event = $this->getEvent(new Request([], [], [
            '_template' => 0,
        ]), null);
        
        $listener->onKernelResponse($event);
        
        // should not throw an exception
    }
    
    public function testViewWithNoLocation()
    {
        $listener = $this->getListener();
        
        $view = self::createMock(View::class);

        $view
            ->method('getLocation')
            ->willReturn('');

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view,
        ]), null);

        $listener->onKernelResponse($event);
        
        // should not throw an exception
    }
    
    public function testWithRoute()
    {
        $view = self::createMock(View::class);

        $route = new Route();
        $route->setRoute('test_show');
        $route->setParams(array(
            'foo' => 'bar',
        ));
        $route->setParameterNames(array());
        
        $view->expects($this->atLeastOnce())
            ->method('getLocation')
            ->willReturn($route);

        $this->router = self::createMock(Router::class);

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('test_show', [
                'foo' => 'bar',
            ])
            ->willReturn('/test/show');
        
        $response = new Response();
        
        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $this->evaluator
            ->expects($this->once())
            ->method('evaluate')
            ->with('bar', $event->getRequest()->attributes->all())
            ->willReturn('bar');
        
        $listener = $this->getListener();
        $listener->onKernelResponse($event);
        
        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('/test/show', $response->headers->get('Location'));
    }

    private function getEvent(Request $request, Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }
        return new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
    }
    
    /**
     * @return LocationResponseListener
     */
    private function getListener()
    {
        return new LocationResponseListener(
            $this->router,
            $this->evaluator
        );
    }
}
