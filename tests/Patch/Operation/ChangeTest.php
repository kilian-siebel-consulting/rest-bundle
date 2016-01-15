<?php
namespace Ibrows\RestBundle\Tests\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation\Change;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit_Framework_TestCase;

class ChangeTest extends PHPUnit_Framework_TestCase
{
    public function testApply()
    {
        $change = new Change();
        $reflectionProperty = new \ReflectionProperty($change, 'value');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($change, 'bar');

        $object = new Something();
        $change->apply($object, new PropertyMetadata($object, 'property'));
        $this->assertEquals('bar', $object->property);
    }
}

class Something
{
    public $property = 'foo';
}