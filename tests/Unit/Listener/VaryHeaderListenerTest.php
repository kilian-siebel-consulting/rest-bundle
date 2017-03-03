<?php
namespace Ibrows\RestBundle\Tests\Unit\Listener;

use Ibrows\RestBundle\Listener\VaryHeaderListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class VaryHeaderListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    public function setUp()
    {
        $this->kernel = self::createMock(HttpKernelInterface::class);
    }

    public function testEmptyBoth()
    {
        $listener = $this->getInstance();
        $response = new Response();
        $event = $this->getEvent($response);
        $listener->onKernelResponse($event);

        $this->assertCount(0, $response->getVary());
    }

    public function testEmptyResponse()
    {
        $listener = $this->getInstance([
            '1',
            '2',
        ]);
        $response = new Response();
        $event = $this->getEvent($response);
        $listener->onKernelResponse($event);

        $this->assertContains('1', $response->getVary());
        $this->assertContains('2', $response->getVary());
        $this->assertCount(2, $response->getVary());
    }

    public function testEmptyListener()
    {
        $listener = $this->getInstance();
        $response = new Response();
        $response->setVary([
            '1',
            '2',
        ]);
        $event = $this->getEvent($response);
        $listener->onKernelResponse($event);

        $this->assertContains('1', $response->getVary());
        $this->assertContains('2', $response->getVary());
        $this->assertCount(2, $response->getVary());
    }

    public function testFilledBoth()
    {
        $listener = $this->getInstance([
            '1',
            '3',
            '4',
        ]);
        $response = new Response();
        $response->setVary([
            '1',
            '2',
        ]);
        $event = $this->getEvent($response);
        $listener->onKernelResponse($event);

        $this->assertContains('1', $response->getVary());
        $this->assertContains('2', $response->getVary());
        $this->assertContains('3', $response->getVary());
        $this->assertContains('4', $response->getVary());
        $this->assertCount(4, $response->getVary());
    }

    /**
     * @param Response $response
     * @return FilterResponseEvent
     */
    private function getEvent(Response $response)
    {
        $request = new Request();

        return new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
    }


    /**
     * @param string[] $headers
     * @return VaryHeaderListener
     */
    private function getInstance(array $headers = [])
    {
        return new VaryHeaderListener([
            'headers' => $headers,
        ]);
    }
}
