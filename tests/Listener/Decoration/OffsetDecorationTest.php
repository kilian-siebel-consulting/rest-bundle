<?php


namespace Ibrows\RestBundle\Tests\Listener\Decoration;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Listener\Decoration\OffsetDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\OffsetRepresentation;
use Ibrows\RestBundle\Tests\Listener\AbstractDecorationTest;

class OffsetDecorationTest extends AbstractDecorationTest
{

    public function testOffsetDecoration(){

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offset' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));

        $listener = $this->setupDecoration(OffsetDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertTrue($event->getControllerResult() instanceof OffsetRepresentation);
    }


    public function testOffsetDecorationSecond(){

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('offset' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));

        $listener = $this->setupDecoration(OffsetDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof OffsetRepresentation);
    }


    public function testOffsetDecorationThird(){

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));

        $listener = $this->setupDecoration(OffsetDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof OffsetRepresentation);
    }


    public function testOffsetDecorationFourth(){

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offset' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, $listEntity);

        $listener = $this->setupDecoration(OffsetDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof OffsetRepresentation);
    }


    public function testOffsetDecorationFifth(){

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offset' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array('hello'));

        $listener = $this->setupDecoration(OffsetDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof OffsetRepresentation);
    }

    public function testOffsetDecorationSixth(){

        // TODO: fix test
        $this->markTestSkipped();
        return;
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offset' => 5));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array());

        $listener = $this->setupDecoration(OffsetDecorationListener::class, $event);
        $listener->onKernelView($event);

        $this->assertTrue($event->getControllerResult() instanceof OffsetRepresentation);
    }

}