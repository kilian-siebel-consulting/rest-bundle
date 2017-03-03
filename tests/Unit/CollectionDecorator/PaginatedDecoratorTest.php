<?php
namespace Ibrows\RestBundle\Tests\Unit\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Ibrows\RestBundle\CollectionDecorator\PaginatedDecorator;
use Ibrows\RestBundle\Model\ApiListableInterface;
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
        $this->paramFetcher = self::createMock(ParamFetcherInterface::class);
    }

    /**
     * @return PaginatedDecorator
     */
    protected function getDecorator()
    {
        return new PaginatedDecorator(
            [
                'page_parameter_name'  => 'page',
                'limit_parameter_name' => 'limit',
            ],
            $this->paramFetcher
        );
    }

    public function testNonCollectionResponse()
    {
        $data = [
            'foo',
        ];

        $result = $this->getDecorator()->decorate(
            new ParameterBag(
                [
                    'paramFetcher' => true,
                    '_route'       => true,
                ]
            ),
            $data
        );

        $this->assertEquals($data, $result);
    }

    public function testMissingParameters()
    {
        $data = new CollectionRepresentation(
            [
                'foo',
            ]
        );

        $result = $this->getDecorator()->decorate(new ParameterBag([]), $data);

        $this->assertEquals($data, $result);
    }

    public function testMissingParamFetchers()
    {
        $data = new CollectionRepresentation(
            [
                new PaginatedTestClass(),
            ]
        );

        $this->paramFetcher
            ->method('get')
            ->willThrowException(new InvalidArgumentException());

        $result = $this->getDecorator()->decorate(
            new ParameterBag(
                [
                    'paramFetcher' => $this->paramFetcher,
                    '_route'       => true,
                ]
            ),
            $data
        );

        $this->assertEquals($data, $result);
    }

    public function testEmptyParamFetchers()
    {
        $data = new CollectionRepresentation(
            [
                new PaginatedTestClass(),
            ]
        );

        $this->paramFetcher
            ->method('get')
            ->willReturn(null);

        $result = $this->getDecorator()->decorate(
            new ParameterBag(
                [
                    'paramFetcher' => $this->paramFetcher,
                    '_route'       => true,
                ]
            ),
            $data
        );

        $this->assertEquals($data, $result);
    }

    public function testCollectionResponse()
    {
        $data = new CollectionRepresentation(
            [
                new PaginatedTestClass(),
            ]
        );

        $this->paramFetcher
            ->method('get')
            ->willReturn(7);

        $result = $this->getDecorator()->decorate(
            new ParameterBag(
                [
                    'paramFetcher' => $this->paramFetcher,
                    '_route'       => true,
                ]
            ),
            $data
        );

        $this->assertInstanceOf(PaginatedRepresentation::class, $result);
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
