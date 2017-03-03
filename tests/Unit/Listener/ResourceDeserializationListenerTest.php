<?php
namespace Ibrows\RestBundle\Tests\Unit\Listener;

use Ibrows\RestBundle\Listener\ResourceDeserializationListener;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\VisitorInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ResourceDeserializationListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TransformerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $transformer;

    /**
     * @var VisitorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $visitor;

    public function setUp()
    {
        $this->transformer = self::createMock(TransformerInterface::class);
        $this->visitor = self::createMock(VisitorInterface::class);
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
                'name'   => 'user_list',
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
            ->will(
                $this->returnCallback(
                    function ($data) {
                        return (is_array($data) ? false : true);
                    }
                )
            );

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
                'name'   => 'user_list',
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
            ->will(
                $this->returnCallback(
                    function ($data) {
                        return (is_array($data) ? false : true);
                    }
                )
            );

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
            ->will(
                $this->returnCallback(
                    function ($className) {
                        return $className === 'a_class';
                    }
                )
            );

        $this->transformer
            ->method('isResourcePath')
            ->willReturn(true);

        $listener->onSerializerPreDeserialize($event);

        $this->assertEquals(
            [
                'name'   => 'array',
                'params' => [
                    [
                        'name'   => 'rest_resource',
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
            ->will(
                $this->returnCallback(
                    function ($className) {
                        return $className === 'a_class';
                    }
                )
            );

        $this->transformer
            ->method('isResourcePath')
            ->willReturn(true);

        $listener->onSerializerPreDeserialize($event);

        $this->assertNotEquals('rest_resource', $event->getType()['name']);
        $this->assertEquals('b_class', $event->getType()['name']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The parameter originalType has to be defined for rest_resource.
     */
    public function testDeserializeMissingType()
    {
        $listener = $this->getListener();
        $listener->deserializeStrict(
            $this->visitor,
            'foobar',
            [],
            DeserializationContext::create()
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The class or interface foobar does not exist.
     */
    public function testDeserializeInvalidType()
    {
        $listener = $this->getListener();
        $listener->deserializeStrict(
            $this->visitor,
            'foobar',
            [
                'params' => [
                    'originalType' => 'foobar'
                ],
            ],
            DeserializationContext::create()
        );
    }

    public function testDeserializeNull()
    {
        $this->transformer
            ->method('getResourceProxy')
            ->willReturn(null);

        $listener = $this->getListener();
        $result = $listener->deserializeStrict(
            $this->visitor,
            'foobar',
            [
                'params' => [
                    'originalType' => self::class,
                ],
            ],
            DeserializationContext::create()
        );
        $this->assertNull($result);
    }

    public function testDeserializeInvalid()
    {
        $this->transformer
            ->method('getResourceProxy')
            ->willReturn('something');

        $listener = $this->getListener();
        $result = $listener->deserializeStrict(
            $this->visitor,
            'foobar',
            [
                'params' => [
                    'originalType' => self::class,
                ],
            ],
            DeserializationContext::create()
        );
        $this->assertNull($result);
    }

    public function testDeserializeValid()
    {
        $this->transformer
            ->method('getResourceProxy')
            ->willReturn($this);

        $listener = $this->getListener();
        $result = $listener->deserializeStrict(
            $this->visitor,
            'foobar',
            [
                'params' => [
                    'originalType' => self::class,
                ],
            ],
            DeserializationContext::create()
        );
        $this->assertEquals($this, $result);
    }

    /**
     * @return ResourceDeserializationListener
     */
    private function getListener()
    {
        return new ResourceDeserializationListener(
            $this->transformer,
            'rest_resource'
        );
    }
}
