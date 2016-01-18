<?php

namespace Ibrows\RestBundle\Tests\Listener\Decoration;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Listener\Decoration\LastIdDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\LastIdRepresentation;
use Ibrows\RestBundle\Tests\Listener\AbstractDecorationTest;

class LastIdDecorationTest extends AbstractDecorationTest
{
    public function testLastIdDecoration()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertTrue($event->getControllerResult() instanceof LastIdRepresentation);
    }

    public function testLastIdDecorationSecond()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof LastIdRepresentation);
    }

    public function testLastIdDecorationThird()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, array($listEntity));

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof LastIdRepresentation);
    }

    public function testLastIdDecorationFourth()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array('helloWorld'));

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof LastIdRepresentation);
    }

    public function testLastIdDecorationFifth()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, 'helloWorld');

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof LastIdRepresentation);
    }


    public function testLastIdDecorationSixth()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $event = $this->getEvent($paramFetcher, $listEntity);

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof LastIdRepresentation);
    }

    public function testLastIdDecorationSeventh()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 10, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $event = $this->getEvent($paramFetcher, array());

        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);

        $listener->onKernelView($event);

        $this->assertFalse($event->getControllerResult() instanceof LastIdRepresentation);
    }


}