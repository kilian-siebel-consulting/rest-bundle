<?php


namespace Ibrows\RestBundle\Tests\Listener;



use Ibrows\RestBundle\Annotation\Route;
use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\Cache\CachePolicy;
use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use Ibrows\RestBundle\Listener\View\CacheHeaderListener;
use Ibrows\RestBundle\Listener\View\LocationResponseListener;
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
        $view = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $view->method('getCachePolicyName')->willReturn('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_view' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array([
                'name' => 'test',
                'timetolife' => 3600,
                'type' => CachePolicy::TYPE_PRIVATE
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('max-age=3600, private', $response->headers->get('Cache-Control'));
    }

    public function testNonPrivateCacheHeader()
    {
        $view = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $view->method('getCachePolicyName')->willReturn('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_view' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array([
                'name' => 'test',
                'timetolife' => 3600,
                'type' => CachePolicy::TYPE_PUBLIC
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('max-age=3600, public', $response->headers->get('Cache-Control'));
    }

    public function testNoStoreCacheHeader()
    {
        $view = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $view->method('getCachePolicyName')->willReturn('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_view' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array([
                'name' => 'test',
                'timetolife' => 3600,
                'type' => CachePolicy::TYPE_NO_STORE
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('no-store, private', $response->headers->get('Cache-Control'));
    }

    public function testNoCacheCacheHeader()
    {
        $view = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $view->method('getCachePolicyName')->willReturn('test');

        $response = new Response();

        $event = $this->getEvent(new Request([], [], [
            '_view' => $view
        ]), $response);

        $listener = new CacheHeaderListener(
            array([
                'name' => 'test',
                'timetolife' => 3600,
                'type' => CachePolicy::TYPE_NO_CACHE
            ])
        );
        $listener->onKernelResponse($event);

        $this->assertEquals($response, $event->getResponse());
        $this->assertEquals('no-cache, private', $response->headers->get('Cache-Control'));
    }

    private function getEvent(Request $request, Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }
        return new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
    }



}