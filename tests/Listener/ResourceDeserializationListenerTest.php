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

        $listener->onPreDeserialize($event);

        $this->assertEquals(ResourceDeserializationListener::TYPE_NAME, $event->getType()['name']);
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

        $listener->onPreDeserialize($event);

        $this->assertEquals(
            [
                'name'   => 'array',
                'params' => [
                    [
                        'name' => ResourceDeserializationListener::TYPE_NAME,
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

        $listener->onPreDeserialize($event);

        $this->assertNotEquals(ResourceDeserializationListener::TYPE_NAME, $event->getType()['name']);
    }

    /**
     * @return ResourceDeserializationListener
     */
    private function getListener()
    {
        return new ResourceDeserializationListener(
            $this->transformer
        );
    }
}