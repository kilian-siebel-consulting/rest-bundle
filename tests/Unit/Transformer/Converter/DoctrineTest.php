<?php
namespace Ibrows\RestBundle\Tests\Unit\Transformer\Converter;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Ibrows\RestBundle\Transformer\Converter\Doctrine;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class DoctrineTest extends PHPUnit_Framework_TestCase
{
    public function testGetResource()
    {
        /** @var ObjectManager|PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = self::createMock(ObjectManager::class);

        $objectManager
            ->expects($this->once())
            ->method('find')
            ->with('class', 'id')
            ->willReturn('foo');

        $converter = new Doctrine($objectManager);

        $this->assertEquals('foo', $converter->getResource('class', 'id'));
    }

    public function testGetResourceProxyObject()
    {
        /** @var ObjectManager|PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = self::createMock(ObjectManager::class);

        $objectManager
            ->expects($this->once())
            ->method('find')
            ->with('class', 'id')
            ->willReturn('foo');

        $converter = new Doctrine($objectManager);

        $this->assertEquals('foo', $converter->getResourceProxy('class', 'id'));
    }

    public function testGetResourceProxyEntity()
    {
        /** @var EntityManagerInterface|PHPUnit_Framework_MockObject_MockObject $objectManager */
        $objectManager = self::createMock(EntityManagerInterface::class);

        $objectManager
            ->expects($this->once())
            ->method('getReference')
            ->with('class', 'id')
            ->willReturn('foo');

        $objectManager
            ->expects($this->never())
            ->method('find');

        $converter = new Doctrine($objectManager);

        $this->assertEquals('foo', $converter->getResourceProxy('class', 'id'));
    }
}
