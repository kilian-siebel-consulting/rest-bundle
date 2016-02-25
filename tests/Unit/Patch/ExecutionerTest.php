<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Ibrows\RestBundle\Patch\AddressLookupInterface;
use Ibrows\RestBundle\Patch\Executioner;
use Ibrows\RestBundle\Patch\OperationApplierInterface;
use Ibrows\RestBundle\Patch\OperationInterface;
use Ibrows\RestBundle\Patch\PointerInterface;
use Ibrows\RestBundle\Patch\ValueInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ExecutionerTest extends PHPUnit_Framework_TestCase
{
    public function testApplierWeight()
    {
        /** @var AddressLookupInterface|PHPUnit_Framework_MockObject_MockObject $addressLookup */
        $addressLookup = $this->getMockForAbstractClass(AddressLookupInterface::class);

        $executioner = new Executioner($addressLookup);

        /** @var OperationApplierInterface|PHPUnit_Framework_MockObject_MockObject $rightOperationApplier */
        $rightOperationApplier = $this->getMockForAbstractClass(OperationApplierInterface::class);
        /** @var OperationApplierInterface|PHPUnit_Framework_MockObject_MockObject $wrongOperationApplier */
        $wrongOperationApplier = $this->getMockForAbstractClass(OperationApplierInterface::class);

        $executioner->addOperationApplier('test', $wrongOperationApplier, 5);
        $executioner->addOperationApplier('test', $rightOperationApplier, 10);
        $executioner->addOperationApplier('test', $wrongOperationApplier, 5);

        $rightOperationApplier
            ->expects(static::once())
            ->method('apply');

        $wrongOperationApplier
            ->expects(static::never())
            ->method('apply');

        /** @var ValueInterface|PHPUnit_Framework_MockObject_MockObject $value */
        $value = $this->getMockForAbstractClass(ValueInterface::class);

        /** @var PointerInterface|PHPUnit_Framework_MockObject_MockObject $pointer */
        $pointer = $this->getMockForAbstractClass(PointerInterface::class);


        $addressLookup
            ->method('lookup')
            ->willReturn($value);

        /** @var OperationInterface|PHPUnit_Framework_MockObject_MockObject $operation */
        $operation = $this->getMockForAbstractClass(OperationInterface::class);
        $operation
            ->method('operation')
            ->willReturn('test');
        $operation
            ->method('pathPointer')
            ->willReturn($pointer);
        $operation
            ->method('fromPointer')
            ->willReturn(null);
        $operation
            ->method('parameters')
            ->willReturn([]);

        $subject = [];

        $executioner->execute([$operation], $subject);
    }
}
