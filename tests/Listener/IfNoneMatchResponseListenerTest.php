<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\RestBundle\Listener\IfNonMatchResponseListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class IfNoneMatchResponseListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    public function setUp()
    {
        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
    }

    public function testMatching()
    {
        $listener = $this->getListener();

        /** @var Response|PHPUnit_Framework_MockObject_MockObject $response */
        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request();

        $response
            ->method('isNotModified')
            ->with($request)
            ->willReturn(true);

        $response
            ->expects($this->once())
            ->method('setNotModified');

        $event = $this->getEvent($request, $response);

        $listener->onKernelResponse($event);
    }

    public function testNotMatching()
    {
        $listener = $this->getListener();

        /** @var Response|PHPUnit_Framework_MockObject_MockObject $response */
        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request();

        $response
            ->method('isNotModified')
            ->with($request)
            ->willReturn(false);

        $response
            ->expects($this->never())
            ->method('setNotModified');

        $event = $this->getEvent($request, $response);

        $listener->onKernelResponse($event);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @return FilterResponseEvent
     */
    private function getEvent(Request $request, Response $response)
    {
        return new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
    }

    /**
     * @return IfNonMatchResponseListener
     */
    private function getListener()
    {
        return new IfNonMatchResponseListener([]);
    }
}