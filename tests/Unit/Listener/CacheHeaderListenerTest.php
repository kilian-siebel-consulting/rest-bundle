<?php


namespace Ibrows\RestBundle\Tests\Unit\Listener;

use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use Ibrows\RestBundle\Listener\CacheHeaderListener;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class CacheHeaderListenerTest extends \PHPUnit_Framework_TestCase
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
        $this->router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->evaluator = $this->getMockBuilder(ExpressionEvaluator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
        $this->context = $this->getMockBuilder(RequestContext::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testPrivateCacheHeader()
    {
        $view = new View();
        $view->setCachePolicyName('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array('test' => [
                'max_age' => 3600,
                'type' => CacheHeaderListener::TYPE_PRIVATE
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('max-age=3600, private', $response->headers->get('Cache-Control'));
    }

    public function testNonPrivateCacheHeader()
    {
        $view = new View();
        $view->setCachePolicyName('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array('test' => [
                'max_age' => 3600,
                'type' => CacheHeaderListener::TYPE_PUBLIC
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('public, s-maxage=3600', $response->headers->get('Cache-Control'));
    }

    public function testNoCache()
    {
        $view = new View();
        $view->setCachePolicyName('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array('test' => [
                'type' => CacheHeaderListener::TYPE_NO_CACHE
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('no-cache', $response->headers->get('Cache-Control'));
    }

    public function testNonExistentCache()
    {
        $view = new View();
        $view->setCachePolicyName('test2');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $listener = new CacheHeaderListener(array());
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('no-cache', $response->headers->get('Cache-Control'));
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWrongConfig()
    {
        $view = new View();
        $view->setCachePolicyName('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array('test' => [
                'max_age' => 3600,
                'type' => 'YOLO'
            ])
        );
        $listener->onKernelResponse($event);
    }

    public function testWrongConfig2()
    {
        $view = new View();

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_template' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array('test' => [
                'max_age' => 3600,
                'type' => CacheHeaderListener::TYPE_PRIVATE
            ])
        );
        $listener->onKernelResponse($event);
        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('no-cache', $response->headers->get('Cache-Control'));
    }

    private function getEvent(Request $request, Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }
        return new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
    }
}
