<?php
namespace Ibrows\RestBundle\Tests\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\CollectionDecorator\OffsetDecorator;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\OffsetRepresentation;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class OffsetDecoratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ParamFetcherInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $paramFetcher;

    public function setUp()
    {
        $this->paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
    }

    public function testNonCollectionResponse()
    {
        $data = [
            'foo',
        ];

        $decorator = new OffsetDecorator();
        $result = $decorator->decorate(new ParameterBag([
            'paramConverter' => true,
            '_route' => true,
        ]), $data);

        $this->assertEquals($data, $result);
    }

    public function testMissingParameters()
    {
        $data = new CollectionRepresentation([
            'foo',
        ]);

        $decorator = new OffsetDecorator();
        $result = $decorator->decorate(new ParameterBag([]), $data);

        $this->assertEquals($data, $result);
    }

    public function testMissingParamFetchers()
    {
        $data = new CollectionRepresentation([
            new OffsetTestClass(),
        ]);

        $this->paramFetcher
            ->method('get')
            ->willThrowException(new InvalidArgumentException());

        $decorator = new OffsetDecorator();
        $result = $decorator->decorate(new ParameterBag([
            'paramConverter' => $this->paramFetcher,
            '_route' => true,
        ]), $data);
        
        $this->assertEquals($data, $result);
    }

    public function testCollectionResponse()
    {
        $data = new CollectionRepresentation([
            new OffsetTestClass(),
        ]);

        $decorator = new OffsetDecorator();
        $result = $decorator->decorate(new ParameterBag([
            'paramConverter' => $this->paramFetcher,
            '_route' => true,
        ]), $data);

        $this->assertInstanceOf(OffsetRepresentation::class, $result);
        $this->assertEquals($data, $result->getInline());
    }
}

class OffsetTestClass implements ApiListableInterface
{
    public function getId()
    {
        return 42;
    }
}