<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\RestBundle\Listener\LinkHeaderListener;
use Ibrows\RestBundle\Request\LinkHeader;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;

class LinkHeaderListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UrlMatcherInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $urlMatcher;

    /**
     * @var ResourceTransformer|PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceTransformer;

    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    /**
     * @var RequestContext|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @param string $method
     * @dataProvider getNonLinkMethods
     */
    public function testNonLinkMethod($method)
    {
        $listener = $this->getListener();
        $event = $this->getEvent();

        $event->getRequest()->setMethod($method);

        $listener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has('links'));
    }

    /**
     * @param string $method
     * @dataProvider getLinkMethods
     */
    public function testLinkMethod($method)
    {
        $listener = $this->getListener();
        $event = $this->getEvent();

        $event->getRequest()->setMethod($method);
        $event->getRequest()->headers->set('Link', 'foo');

        $this->context->method('getMethod')->willReturn('METHOD');

        $listener->onKernelRequest($event);

        $this->assertTrue($event->getRequest()->attributes->has('links'));
    }

    /**
     * @param string $method
     * @dataProvider getLinkMethods
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Please specify at least one Link.
     */
    public function testNoLinks($method)
    {
        $listener = $this->getListener();
        $event = $this->getEvent();

        $event->getRequest()->setMethod($method);

        $this->context->method('getMethod')->willReturn('METHOD');

        $listener->onKernelRequest($event);
    }

    /**
     * @param string $method
     * @dataProvider getLinkMethods
     */
    public function testProducedLinks($method)
    {
        $listener = $this->getListener();
        $event = $this->getEvent();

        $urlParams = [
            'foo' => 'bar',
        ];
        $this->urlMatcher
            ->expects($this->exactly(3))
            ->method('match')
            ->willReturn($urlParams);

        $event->getRequest()->setMethod($method);
        $event->getRequest()->headers->set('Link', 'link1,link2, link3');

        $this->context
            ->method('getMethod')
            ->willReturn('METHOD')
        ;
        $this->context
            ->expects($this->exactly(2))
            ->method('setMethod')
            ->withConsecutive(
                [ 'GET' ],
                [ 'METHOD' ]
            )
        ;

        $listener->onKernelRequest($event);

        $this->assertTrue($event->getRequest()->attributes->has('links'));

        /** @var LinkHeader[] $links */
        $links = $event->getRequest()->attributes->get('links');

        $this->assertEquals(3, count($links));
        $this->assertEquals('link1', $links[0]->getOriginalHeader());
        $this->assertEquals($urlParams, $links[0]->getUrlParameters());
        $this->assertEquals('link2', $links[1]->getOriginalHeader());
        $this->assertEquals('link3', $links[2]->getOriginalHeader());
    }

    public function setUp()
    {
        $this->urlMatcher = $this->getMockForAbstractClass(UrlMatcherInterface::class);
        $this->resourceTransformer = $this->getMockBuilder(ResourceTransformer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
        $this->context = $this->getMockBuilder(RequestContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlMatcher->method('getContext')->willReturn($this->context);
    }

    /**
     * @return LinkHeaderListener
     */
    private function getListener()
    {
        return new LinkHeaderListener(
            $this->urlMatcher,
            $this->resourceTransformer
        );
    }

    /**
     * @return KernelEvent
     */
    private function getEvent()
    {
        return new KernelEvent(
            $this->kernel,
            new Request(),
            'whatever'
        );
    }

    /**
     * @return string[]
     */
    public function getNonLinkMethods()
    {
        return [
            [ Request::METHOD_HEAD ],
            [ Request::METHOD_GET ],
            [ Request::METHOD_POST ],
            [ Request::METHOD_PUT ],
            [ Request::METHOD_PATCH ],
            [ Request::METHOD_DELETE ],
            [ Request::METHOD_PURGE ],
            [ Request::METHOD_OPTIONS ],
            [ Request::METHOD_TRACE ],
            [ Request::METHOD_CONNECT ],
        ];
    }

    /**
     * @return string[]
     */
    public function getLinkMethods()
    {
        return [
            [ 'LINK' ],
            [ 'link' ],
            [ 'UNLINK' ],
            [ 'unlink' ],
        ];
    }
}