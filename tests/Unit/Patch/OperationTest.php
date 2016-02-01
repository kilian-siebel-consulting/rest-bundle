<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Ibrows\RestBundle\Patch\Operation;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit_Framework_TestCase;

class OperationTest extends PHPUnit_Framework_TestCase
{
    public function testGetPath()
    {
        $operation = new SomeOperation();
        $reflectionProperty = new \ReflectionProperty(Operation::class, 'path');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($operation, 'foo');

        $this->assertEquals('foo', $operation->getPath());
    }
}

class SomeOperation extends Operation
{
    public function apply($object, PropertyMetadata $property)
    {}
}
