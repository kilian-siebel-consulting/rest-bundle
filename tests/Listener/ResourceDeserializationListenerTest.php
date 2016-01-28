<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\RestBundle\Listener\ResourceDeserializationListener;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ResourceDeserializationListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TransformerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $transformer;

    public function setUp()
    {
        $this->transformer = $this->getMockForAbstractClass(TransformerInterface::class);
    }

    public function testWithResource()
    {
        $listener = $this->getListener();

        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            'anything',
            [
                'name'   => 'a_class',
                'params' => [],
            ]
        );

        $this->transformer
            ->method('isResource')
            ->will($this->returnCallback(function($className) {
                return $className === 'a_class';
            }));

        $listener->onSerializerPreDeserialize($event);

        $this->assertEquals('rest_resource', $event->getType()['name']);
    }

    public function testWithResourceArray()
    {
        $listener = $this->getListener();

        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            'anything',
            [
                'name'   => 'array',
                'params' => [
                    [
                        'name' => 'a_class'
                    ]
                ],
            ]
        );

        $this->transformer
            ->method('isResource')
            ->will($this->returnCallback(function($className) {
                return $className === 'a_class';
            }));

        $listener->onSerializerPreDeserialize($event);

        $this->assertEquals(
            [
                'name'   => 'array',
                'params' => [
                    [
                        'name' => 'rest_resource',
                        'params' => [
                                'originalType' => 'a_class'
                        ]
                    ]
                ],
            ],
            $event->getType()
        );
    }

    public function testWithNoResource()
    {
        $listener = $this->getListener();

        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            'anything',
            [
                'name'   => 'b_class',
                'params' => [],
            ]
        );

        $this->transformer
            ->method('isResource')
            ->will($this->returnCallback(function($className) {
                return $className === 'a_class';
            }));

        $listener->onSerializerPreDeserialize($event);

        $this->assertNotEquals('rest_resource', $event->getType()['name']);
        $this->assertEquals('b_class', $event->getType()['name']);
    }

    /**
     * @return ResourceDeserializationListener
     */
    private function getListener()
    {
        return new ResourceDeserializationListener(
            $this->transformer,'rest_resource'
        );
    }
}
