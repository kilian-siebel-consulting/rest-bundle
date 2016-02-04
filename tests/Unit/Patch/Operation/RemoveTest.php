<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation\Remove;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit_Framework_TestCase;

class RemoveTest extends PHPUnit_Framework_TestCase
{
    public function testApply()
    {
        $change = new Remove();

        $object = new SomethingElse();
        $change->apply($object, new PropertyMetadata($object, 'property'));
        $this->assertNull($object->property);
    }
}

class SomethingElse
{
    public $property = 'foo';
}
