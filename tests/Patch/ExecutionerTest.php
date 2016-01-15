<?php
namespace Ibrows\RestBundle\Tests\Patch;

use Ibrows\RestBundle\Patch\Executioner;
use Ibrows\RestBundle\Patch\Operation;
use Ibrows\RestBundle\Patch\OperationInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\Driver\DriverInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ExecutionerListener extends PHPUnit_Framework_TestCase
{
    public function testExecution()
    {
        /** @var DriverInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this->getMockForAbstractClass(DriverInterface::class);

        /** @var ClassMetadata|PHPUnit_Framework_MockObject_MockObject $classMetadata */
        $classMetadata = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $driver->method('loadMetadataForClass')->willReturn($classMetadata);

        $propertyMetadata = new PropertyMetadata(TestObject::class, 'property');

        $classMetadata->propertyMetadata = [
            $propertyMetadata,
        ];

        $object = new TestObject();

        /** @var Operation|PHPUnit_Framework_MockObject_MockObject $operation */
        $operation = $this->getMockForAbstractClass(OperationInterface::class);
        $operation
            ->method('getPath')
                ->willReturn('/property')
        ;
        $operation
            ->expects($this->once())
            ->method('apply')
            ->with($object, $propertyMetadata)
        ;

        $executioner = new Executioner($driver);
        $executioner->execute($object, [
            $operation,
        ]);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Property /invalid does not exist or is not writable.
     */
    public function testInvalidPath()
    {
        /** @var DriverInterface|PHPUnit_Framework_MockObject_MockObject $driver */
        $driver = $this->getMockForAbstractClass(DriverInterface::class);

        /** @var ClassMetadata|PHPUnit_Framework_MockObject_MockObject $classMetadata */
        $classMetadata = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
        $driver->method('loadMetadataForClass')->willReturn($classMetadata);

        $object = new TestObject();

        /** @var Operation|PHPUnit_Framework_MockObject_MockObject $operation */
        $operation = $this->getMockForAbstractClass(OperationInterface::class);
        $operation
            ->method('getPath')
            ->willReturn('/invalid')
        ;
        $operation
            ->expects($this->never())
            ->method('apply')
        ;

        $executioner = new Executioner($driver);
        $executioner->execute($object, [
            $operation,
        ]);
    }
}

class TestObject
{
    private $property;

    public function getProperty()
    {
        return $this->property;
    }
}