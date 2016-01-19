<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\RestBundle\Listener\EtagResponseListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class EtagResponseListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    public function setUp()
    {
        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
    }

    /**
     * @dataProvider getMethodsProvider
     * @param string $method
     */
    public function testCreateEtag($method)
    {
        $listener = $this->getListener();

        $request = new Request();
        $request->setMethod($method);

        $content = 'foobar';

        $response = new Response($content, 200);

        $event = $this->getEvent($request, $response);

        $listener->onKernelResponse($event);

        $this->assertEquals('"' . md5($content) . '"', $response->getEtag());
    }

    /**
     * @dataProvider otherMethodsProvider
     * @param string $method
     */
    public function testNonGet($method)
    {
        $listener = $this->getListener();

        $request = new Request();
        $request->setMethod($method);

        $content = 'foobar';

        $response = new Response($content, 200);

        $event = $this->getEvent($request, $response);

        $listener->onKernelResponse($event);

        $this->assertNull($response->getEtag());
    }

    /**
     * @dataProvider getMethodsProvider
     * @param string $method
     */
    public function testNonSuccess($method)
    {
        $listener = $this->getListener();

        $request = new Request();
        $request->setMethod($method);

        $content = 'foobar';

        $response = new Response($content, 400);

        $event = $this->getEvent($request, $response);

        $listener->onKernelResponse($event);

        $this->assertNull($response->getEtag());
    }

    /**
     * @return string[][]
     */
    public function getMethodsProvider()
    {
        return [
            [ Request::METHOD_GET ],
            [ Request::METHOD_HEAD ],
        ];
    }

    /**
     * @return string[][]
     */
    public function otherMethodsProvider()
    {
        return [
            [ Request::METHOD_POST ],
            [ Request::METHOD_PUT ],
            [ Request::METHOD_DELETE ],
            [ Request::METHOD_PATCH ],
            [ Request::METHOD_OPTIONS ],
            [ Request::METHOD_PURGE ],
            [ Request::METHOD_TRACE ],
            [ Request::METHOD_CONNECT ],
        ];
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
     * @param string $algorithm
     * @return EtagResponseListener
     */
    private function getListener($algorithm = 'md5')
    {
        return new EtagResponseListener([
            'enabled' => true,
            'hashing_algorithm' => $algorithm,
        ]);
    }
}