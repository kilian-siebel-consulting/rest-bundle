<?php
namespace Ibrows\RestBundle\Tests\JMS;

use Ibrows\RestBundle\Patch\ResourceDeserializationHandler;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use InvalidArgumentException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\VisitorInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ResourceDeserializationHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TransformerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $transformer;

    /**
     * @var VisitorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $visitor;

    /**
     * @var DeserializationContext
     */
    private $context;

    private $typeName = 'ibrows_rest_resource_weak';

    public function setUp()
    {
        $this->transformer = $this->getMockForAbstractClass(TransformerInterface::class);
        $this->visitor = $this->getMockForAbstractClass(VisitorInterface::class);
        $this->context = DeserializationContext::create();
    }

    public function testHandlerWithResource()
    {
        $handler = $this->getHandler();

        $resource = new \DateTime();

        $this->transformer
            ->method('getResourceProxy')
            ->willReturn($resource);

        $result = $handler->deserializeWeak(
            $this->visitor,
            'resource',
            [
                'params' => [
                    'originalType' => \DateTime::class
                ]
            ],
            $this->context
        );

        $this->assertEquals($resource, $result);
    }

    public function testHandlerWithInvalidOriginalType()
    {
        $handler = $this->getHandler();

        $resource = new \DateTime();

        $this->transformer
            ->method('getResourceProxy')
            ->willReturn($resource);

        $result = $handler->deserializeWeak(
            $this->visitor,
            'resource',
            [
                'params' => [
                    'originalType' => '\This\Class\Does\Not\Exists\Schwurbel'
                ]
            ],
            $this->context
        );

        $this->assertEquals($resource, $result);
    }

    public function testHandlerWithNonResource()
    {
        $handler = $this->getHandler();

        $this->transformer
            ->method('getResourceProxy')
            ->willReturn(null);

        $result = $handler->deserializeWeak(
            $this->visitor,
            'something else',
            [],
            $this->context
        );

        $this->assertEquals('something else', $result);
    }

    public function testHandlerWithInvalidResource()
    {
        $handler = $this->getHandler();

        $this->transformer
            ->method('getResourceProxy')
            ->willThrowException(new InvalidArgumentException());

        $result = $handler->deserializeWeak(
            $this->visitor,
            'something else',
            [],
            $this->context
        );

        $this->assertEquals('something else', $result);
    }

    private function getHandler()
    {
        return new ResourceDeserializationHandler(
            $this->transformer,
            $this->typeName

        );
    }
}
