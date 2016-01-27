<?php
namespace Ibrows\RestBundle\Tests\JMS;

use Ibrows\RestBundle\JMS\ApiResourceHandler;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use InvalidArgumentException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\VisitorInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

class ApiResourceHandlerTest extends PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        $this->transformer = $this->getMockForAbstractClass(TransformerInterface::class);
        $this->visitor = $this->getMockForAbstractClass(VisitorInterface::class);
        $this->context = DeserializationContext::create();
    }

    public function testHandlerWithResource()
    {
        $handler = $this->getHandler();

        $resource = 'im a resource';

        $this->transformer
            ->method('getResourceProxy')
            ->willReturn($resource);

        $result = $handler->deserialize(
            $this->visitor,
            'resource',
            [],
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

        $result = $handler->deserialize(
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

        $result = $handler->deserialize(
            $this->visitor,
            'something else',
            [],
            $this->context
        );

        $this->assertEquals('something else', $result);
    }

    /**
     * @return ApiResourceHandler
     */
    private function getHandler()
    {
        return new ApiResourceHandler(
            $this->transformer
        );
    }
}
