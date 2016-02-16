<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Countable;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;
use Ibrows\RestBundle\Patch\OperationCheckerInterface;
use Ibrows\RestBundle\Patch\OperationFactoryInterface;
use Ibrows\RestBundle\Patch\OperationInterface;
use Ibrows\RestBundle\Patch\PatchConverter;
use Ibrows\RestBundle\Patch\PointerFactoryInterface;
use Ibrows\RestBundle\Patch\PointerInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Traversable;

class PatchConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OperationCheckerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $operationChecker;

    /**
     * @var PointerFactoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $pointerFactory;

    /**
     * @var OperationFactoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $operationFactory;

    public function setUp()
    {
        $this->operationChecker = $this->getMockForAbstractClass(OperationCheckerInterface::class);
        $this->pointerFactory = $this->getMockForAbstractClass(PointerFactoryInterface::class);
        $this->operationFactory = $this->getMockForAbstractClass(OperationFactoryInterface::class);
    }

    /**
     * @expectedException \Ibrows\RestBundle\Patch\Exception\OperationInvalidException
     */
    public function testThrowsOperationInvalidException()
    {
        $this->operationChecker
            ->method('validate')
            ->willThrowException(new OperationInvalidException());

        $converter = $this->getInstance();

        $converter->convert(
            [
                [
                    'some' => 'values',
                ]
            ]
        );
    }

    public function testValidWithoutValue()
    {
        $rawPatch = [
            'op'        => 'some operation',
            'path'      => '/path',
            'parameter' => 'some parameter',
        ];

        $pointer = $this->getMockForAbstractClass(PointerInterface::class);
        $operation = $this->getMockForAbstractClass(OperationInterface::class);

        $this->operationChecker
            ->expects($this->atLeastOnce())
            ->method('validate')
            ->with($rawPatch);

        $this->pointerFactory
            ->expects($this->atLeastOnce())
            ->method('createFromPath')
            ->with('/path')
            ->willReturn($pointer);

        $this->operationFactory
            ->expects($this->atLeastOnce())
            ->method('create')
            ->with(
                'some operation',
                $pointer,
                null,
                null,
                [
                    'parameter' => 'some parameter',
                ]
            )
            ->willReturn($operation);

        $converter = $this->getInstance();

        $operations = $converter->convert([$rawPatch]);

        if (is_array($operations)) {
            $this->assertInternalType('array', $operations);
        } else {
            $this->assertInstanceOf(Traversable::class, $operations);
            $this->assertInstanceOf(Countable::class, $operations);
        }
        $this->assertCount(1, $operations);

        foreach ($operations as $resultOperation) {
            // Prevent "This test performed an assertion on a test double"
            $this->assertTrue($operation === $resultOperation);
        }
    }

    public function testValidWithValue()
    {
        $rawPatch = [
            'op'        => 'some operation',
            'path'      => '/path',
            'parameter' => 'some parameter',
            'value'     => 'some value',
        ];

        $pointer = $this->getMockForAbstractClass(PointerInterface::class);
        $operation = $this->getMockForAbstractClass(OperationInterface::class);

        $this->operationChecker
            ->expects($this->atLeastOnce())
            ->method('validate')
            ->with($rawPatch);

        $this->pointerFactory
            ->expects($this->atLeastOnce())
            ->method('createFromPath')
            ->with('/path')
            ->willReturn($pointer);

        $this->operationFactory
            ->expects($this->atLeastOnce())
            ->method('create')
            ->with(
                'some operation',
                $pointer,
                null,
                'some value',
                [
                    'parameter' => 'some parameter',
                ]
            )
            ->willReturn($operation);

        $converter = $this->getInstance();

        $operations = $converter->convert([$rawPatch]);

        if (is_array($operations)) {
            $this->assertInternalType('array', $operations);
        } else {
            $this->assertInstanceOf(Traversable::class, $operations);
            $this->assertInstanceOf(Countable::class, $operations);
        }
        $this->assertCount(1, $operations);

        foreach ($operations as $resultOperation) {
            // Prevent "This test performed an assertion on a test double"
            $this->assertTrue($operation === $resultOperation);
        }
    }

    /**
     * @return PatchConverter
     */
    private function getInstance()
    {
        return new PatchConverter(
            $this->operationChecker,
            $this->pointerFactory,
            $this->operationFactory
        );
    }
}
