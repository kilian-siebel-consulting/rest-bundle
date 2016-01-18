<?php
namespace Ibrows\RestBundle\Tests\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\CollectionDecorator\PaginatedDecorator;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\PaginationRepresentation;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;

class PaginatedDecoratorTest extends PHPUnit_Framework_TestCase
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

        $decorator = new PaginatedDecorator();
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

        $decorator = new PaginatedDecorator();
        $result = $decorator->decorate(new ParameterBag([]), $data);

        $this->assertEquals($data, $result);
    }

    public function testMissingParamFetchers()
    {
        $data = new CollectionRepresentation([
            new PaginatedTestClass(),
        ]);

        $this->paramFetcher
            ->method('get')
            ->willThrowException(new InvalidArgumentException());

        $decorator = new PaginatedDecorator();
        $result = $decorator->decorate(new ParameterBag([
            'paramConverter' => $this->paramFetcher,
            '_route' => true,
        ]), $data);

        $this->assertEquals($data, $result);
    }

    public function testEmptyParamFetchers()
    {
        $data = new CollectionRepresentation([
            new PaginatedTestClass(),
        ]);

        $this->paramFetcher
            ->method('get')
            ->willReturn(null);

        $decorator = new PaginatedDecorator();
        $result = $decorator->decorate(new ParameterBag([
            'paramConverter' => $this->paramFetcher,
            '_route' => true,
        ]), $data);

        $this->assertEquals($data, $result);
    }

    public function testCollectionResponse()
    {
        $data = new CollectionRepresentation([
            new PaginatedTestClass(),
        ]);

        $this->paramFetcher
            ->method('get')
            ->willReturn(7);

        $decorator = new PaginatedDecorator();
        $result = $decorator->decorate(new ParameterBag([
            'paramConverter' => $this->paramFetcher,
            '_route' => true,
        ]), $data);

        $this->assertInstanceOf(PaginationRepresentation::class, $result);
        $this->assertEquals($data, $result->getInline());
    }
}

class PaginatedTestClass implements ApiListableInterface
{
    public function getId()
    {
        return 42;
    }
}