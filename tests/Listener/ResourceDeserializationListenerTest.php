<?php
namespace Ibrows\RestBundle\Tests\Listener;

use Ibrows\RestBundle\Listener\ResourceDeserializationListener;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\MetadataFactoryInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ResourceDeserializationListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataFactoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataFactory;

    /**
     * @var TransformerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $transformer;

    public function setUp()
    {
        $this->metadataFactory = $this->getMockForAbstractClass(MetadataFactoryInterface::class);
        $this->transformer = $this->getMockForAbstractClass(TransformerInterface::class);
        $this->setUpMetaDataFactory();
    }

    public function testEveryPossibility()
    {
        $listener = $this->getListener();

        $testResource = new TestResourceDestination(7);

        $data = [
            'normalProperty'    => 'normalValue',
            'normalResource'    => [
                'property' => 'value',
            ],
            'normalArray'       => [
                'property' => 'value',
                'arrayProperty' => [
                    'foo',
                ],
            ],
            'resource'          => '/resources/7',
            'resourceUndefined' => '/resources/undefined',
            'resourceArray'     => [
                '/resources/7',
                '/resources/undefined',
                '/resources/7',
            ],
        ];

        $event = new PreDeserializeEvent(
            DeserializationContext::create(),
            $data,
            [
                'name' => TestResourceSource::class
            ]
        );

        $this->transformer
            ->method('isResource')
            ->will(
                $this->returnCallback(
                    function ($className) {
                        return $className === TestResourceDestination::class;
                    }
                )
            );

        $this->transformer
            ->method('getResourceProxy')
            ->will(
                $this->returnCallback(
                    function ($path) use ($testResource) {
                        return $path === '/resources/undefined'
                            ? null
                            : $testResource;
                    }
                )
            );

        $listener->onPreDeserialize($event);

        $this->assertEquals(
            [
                'normalProperty'    => 'normalValue',
                'normalResource'    => [
                    'property' => 'value',
                ],
                'normalArray'       => [
                    'property' => 'value',
                    'arrayProperty' => [
                        'foo',
                    ],
                ],
                'resource'          => $testResource,
                'resourceUndefined' => '/resources/undefined',
                'resourceArray'     => [
                    $testResource,
                    '/resources/undefined',
                    $testResource,
                ],
            ],
            $event->getData()
        );
    }

    private function setUpMetaDataFactory()
    {
        $source = new ClassMetadata(TestResourceSource::class);

        $normalProperty = new PropertyMetadata(TestResourceSource::class, 'normalProperty');
        $normalProperty->setType('string');

        $normalResource = new PropertyMetadata(TestResourceSource::class, 'normalResource');
        $normalResource->setType(TestNormalDestination::class);

        $normalArray = new PropertyMetadata(TestResourceSource::class, 'normalArray');
        $normalArray->setType('array<' . TestNormalDestination::class . '>');

        $resource = new PropertyMetadata(TestResourceSource::class, 'resource');
        $resource->setType(TestResourceDestination::class);

        $resourceUndefined = new PropertyMetadata(TestResourceSource::class, 'resourceUndefined');
        $resourceUndefined->setType(TestResourceDestination::class);

        $resourceArray = new PropertyMetadata(TestResourceSource::class, 'resourceArray');
        $resourceArray->setType('array<' . TestResourceDestination::class . '>');

        $source->propertyMetadata = [
            'normalProperty'    => $normalProperty,
            'normalResource'    => $normalResource,
            'normalArray'       => $normalArray,
            'resource'          => $resource,
            'resourceUndefined' => $resourceUndefined,
            'resourceArray'     => $resourceArray,
        ];

        $this->metadataFactory
            ->method('getMetadataForClass')
            ->willReturn($source);
    }

    /**
     * @return ResourceDeserializationListener
     */
    private function getListener()
    {
        return new ResourceDeserializationListener(
            $this->metadataFactory,
            $this->transformer
        );
    }
}

class TestResourceSource
{
    protected $normalProperty;
    protected $normalResource;
    protected $normalArray;
    protected $resource;
    protected $resourceUndefined;
    protected $resourceArray;
}

class TestResourceDestination
{
}

class TestNormalDestination
{
}