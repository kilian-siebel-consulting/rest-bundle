<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation\Change;
use Ibrows\RestBundle\Patch\Operation\ValueOperation;
use JMS\Serializer\Metadata\PropertyMetadata;
use PHPUnit_Framework_TestCase;

class ChangeTest extends PHPUnit_Framework_TestCase
{
    public function testApply()
    {
        $change = new Change();
        $reflectionProperty = new \ReflectionProperty(ValueOperation::class, 'value');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($change, 'bar');

        $object = new Something();
        $change->apply($object, new PropertyMetadata($object, 'property'));
        $this->assertEquals('bar', $object->property);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testInvalidApply()
    {
        $change = new Change();

        $reflectionProperty = new \ReflectionProperty(ValueOperation::class, 'value');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($change, new \DateTime());

        $object = new Something();
        
        $propertyMetadata = new PropertyMetadata($object, 'property');
        $propertyMetadata->setType(Something::class);
        
        $change->apply($object, $propertyMetadata);
        $this->assertEquals('bar', $object->property);
    }
}

class Something
{
    public $property = 'foo';
}
