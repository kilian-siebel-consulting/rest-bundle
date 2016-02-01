<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\AppBundle\Entity\User;
use Ibrows\RestBundle\Listener\ResourceDeserializationListener;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
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
        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            '/user/1',
            [
                'name'   => 'user',
                'params' => [],
            ]
        );

        $this->transformer
            ->method('isResourcePath')
            ->willReturn(true);

        $this->transformer
            ->method('isResource')
            ->willReturn(true);

        $listener = $this->getListener();

        $listener->onSerializerPreDeserialize($event);

        $this->assertEquals('rest_resource', $event->getType()['name']);
    }

    public function testWithArrayResource()
    {
        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            ['/user/1'],
            [
                'name' => 'user_list',
                'params' => [
                    [
                        'name'   => 'user',
                        'params' => []
                    ]
                ],
            ]
        );

        $this->transformer
            ->method('isResourcePath')
            ->will($this->returnCallback(function($data) {
                return (is_array($data) ? false : true);
            }));

        $this->transformer
            ->method('isResource')
            ->willReturn(true);

        $listener = $this->getListener();

        $listener->onSerializerPreDeserialize($event);

        $this->assertEquals('rest_resource', $event->getType()['params'][0]['name']);
    }


    public function testWithCrappyArrayResource()
    {
        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            ['foobar'],
            [
                'name' => 'user_list',
                'params' => [
                    [
                        'name'   => 'user',
                        'params' => []
                    ]
                ],
            ]
        );

        $this->transformer
            ->method('isResourcePath')
            ->will($this->returnCallback(function($data) {
                return (is_array($data) ? false : true);
            }));

        $this->transformer
            ->method('isResource')
            ->willReturn(true);

        $listener = $this->getListener();

        $listener->onSerializerPreDeserialize($event);

        $this->assertEquals('rest_resource', $event->getType()['params'][0]['name']);
        $this->assertEquals('user_list', $event->getType()['name']);
    }


    public function testWithResourceArray()
    {
        $listener = $this->getListener();

        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            array('/hello/1'),
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

        $this->transformer
            ->method('isResourcePath')
            ->willReturn(true);

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
            '/foo/1',
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

        $this->transformer
            ->method('isResourcePath')
            ->willReturn(true);

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
