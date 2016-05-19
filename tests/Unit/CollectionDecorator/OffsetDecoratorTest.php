<?php
namespace Ibrows\RestBundle\Tests\Unit\CollectionDecorator;

use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\OffsetRepresentation;
use Ibrows\RestBundle\CollectionDecorator\OffsetDecorator;
use Ibrows\RestBundle\Model\ApiListableInterface;
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

    /**
     * @return OffsetDecorator
     */
    protected function getDecorator()
    {
        return new OffsetDecorator(
            [
                'offset_parameter_name' => 'offset',
                'limit_parameter_name'  => 'limit',
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
                new OffsetTestClass(),
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

    public function testCollectionResponse()
    {
        $data = new CollectionRepresentation(
            [
                new OffsetTestClass(),
            ]
        );

        $result = $this->getDecorator()->decorate(
            new ParameterBag(
                [
                    'paramFetcher' => $this->paramFetcher,
                    '_route'       => true,
                ]
            ),
            $data
        );

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
