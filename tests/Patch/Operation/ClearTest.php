<?php
namespace Ibrows\RestBundle\Tests\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation\Clear;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit_Framework_TestCase;

class ClearTest extends PHPUnit_Framework_TestCase
{
    public function testApply()
    {
        $change = new Clear();

        $object = new SomethingElse();
        $change->apply($object, new PropertyMetadata($object, 'property'));
        $this->assertNull($object->property);
    }
}

class SomethingElse
{
    public $property = 'foo';
}