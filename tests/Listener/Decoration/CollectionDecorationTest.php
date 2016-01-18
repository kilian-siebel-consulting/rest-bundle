<?php


namespace Ibrows\RestBundle\Tests\Listener\Decoration;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Listener\Decoration\CollectionDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Tests\Listener\AbstractDecorationTest;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CollectionDecorationTest extends AbstractDecorationTest
{
    public function testDecoration()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 1, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());


        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));
        $listener = $this->setupDecoration(CollectionDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertTrue($event->getControllerResult() instanceof CollectionRepresentation);
    }

}