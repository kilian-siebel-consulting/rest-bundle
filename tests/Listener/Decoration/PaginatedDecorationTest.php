<?php


namespace Ibrows\RestBundle\Tests\Listener\Decoration;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\PaginatedRepresentation;
use Ibrows\RestBundle\Listener\Decoration\PaginatedDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Tests\Listener\AbstractDecorationTest;

class PaginatedDecorationTest extends AbstractDecorationTest
{

    public function testPaginationDecoration ( ) {

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'page' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertTrue($event->getControllerResult() instanceof PaginatedRepresentation);
    }


    public function testPaginationDecorationSecond ( ) {

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('page' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof PaginatedRepresentation);
    }

    public function testPaginationDecorationThird ( ) {

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof PaginatedRepresentation);
    }


    public function testPaginationDecorationFourth ( ) {

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'page' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, $listEntity);
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof PaginatedRepresentation);
    }

    public function testPaginationDecorationFifth ( ) {

        // TODO: fix test
        $this->markTestSkipped();
        return;
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'page' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array());
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertTrue($event->getControllerResult() instanceof PaginatedRepresentation);
    }

    public function testPaginationDecorationSixth ( ) {

        // TODO: fix test
        $this->markTestSkipped();
        return;
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => null, 'page' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array());
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof PaginatedRepresentation);
    }


    public function testPaginationDecorationSeventh ( ) {

        // TODO: fix test
        $this->markTestSkipped();
        return;
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => null, 'page' => null));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array());
        $listener = $this->setupDecoration(PaginatedDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof PaginatedRepresentation);
    }
}