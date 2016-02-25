<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Ibrows\RestBundle\Patch\Operation;
use Ibrows\RestBundle\Patch\PointerInterface;
use PHPUnit_Framework_TestCase;

class OperationTest extends PHPUnit_Framework_TestCase
{
    public function testPublicInterface()
    {
        /** @var PointerInterface $pointer */
        $pointer = $this->getMockForAbstractClass(PointerInterface::class);
        $operation = new Operation(
            'name',
            $pointer,
            null,
            'value',
            [
                'param' => 'value',
            ]
        );

        $this->assertEquals('name', $operation->operation());
        // Prevent This test performed an assertion on a test double
        $this->assertTrue($pointer === $operation->pathPointer());
        $this->assertNull($operation->fromPointer());
        $this->assertEquals('value', $operation->value());
        $this->assertEquals(
            [
                'param' => 'value',
            ],
            $operation->parameters()
        );
    }
}
