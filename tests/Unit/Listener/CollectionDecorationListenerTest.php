<?php
namespace Ibrows\RestBundle\Tests\Unit\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Ibrows\RestBundle\Annotation\View;
use Ibrows\RestBundle\CollectionDecorator\DecoratorInterface;
use Ibrows\RestBundle\Listener\CollectionDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Transformer\TransformerInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CollectionDecorationListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TransformerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceTransformer;

    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    /**
     * @var DecoratorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $decorator;

    /**
     * @var ParamFetcherInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $paramFetcher;

    public function setUp()
    {
        $this->resourceTransformer = $this->getMockForAbstractClass(TransformerInterface::class);
        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
        $this->decorator = $this->getMockForAbstractClass(DecoratorInterface::class);
        $this->paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
    }

    /**
     * @return CollectionDecorationListener
     */
    protected function getListener()
    {
        $listener = new CollectionDecorationListener([], $this->resourceTransformer);

        $listener->addDecorator($this->decorator);

        return $listener;
    }

    /**
     * @param mixed $data
     * @return GetResponseForControllerResultEvent
     */
    protected function getEvent($data)
    {
        return new GetResponseForControllerResultEvent(
            $this->kernel,
            new Request(
                [],
                [],
                [
                    'paramFetcher' => $this->paramFetcher,
                ]
            ),
            HttpKernelInterface::MASTER_REQUEST,
            $data
        );
    }

    /**
     * @param mixed $data
     *
     * @dataProvider getInvalidData
     */
    public function testInvalidData($data)
    {
        $newData = $data;
        if (is_object($data)) {
            $newData = clone $data;
        }

        $event = $this->getEvent($newData);

        $this->getListener()->onKernelView($event);

        $this->assertEquals($data, $event->getControllerResult());
    }

    public function getInvalidData()
    {
        return [
            [7],
            [new stdClass()],
            [
                new ArrayCollection(
                    [
                        'foo'
                    ]
                )
            ],
            [[]],
        ];
    }

    public function testValidData()
    {
        $data = [
            new TestResource(),
            new TestResource(),
            new TestResource(),
        ];

        $event = $this->getEvent($data);

        $this->decorator
            ->expects($this->once())
            ->method('decorate')
            ->will(
                $this->returnCallback(
                    function (ParameterBag $params, $collection) {
                        return [
                            $collection,
                        ];
                    }
                )
            );

        $this->getListener()->onKernelView($event);

        $result = $event->getControllerResult();

        $this->assertInternalType('array', $result);

        /** @var CollectionRepresentation $collection */
        $collection = array_shift($result);

        $this->assertInstanceOf(CollectionRepresentation::class, $collection);

        $this->assertEquals($data, $collection->getResources());
    }

    public function testSerializationGroups()
    {
        $data = [
            new TestResource(),
        ];

        $event = $this->getEvent($data);

        $view = new View([]);
        $view->setSerializerGroups(['foo']);

        $event->getRequest()->attributes->set('_view', $view);

        $this->getListener()->onKernelView($event);

        $this->assertEquals(
            [
                'foo',
                'hateoas_list',
            ],
            $view->getSerializerGroups()
        );
    }

    public function testCollectionResponse () {

        $this->decorator
            ->expects($this->once())
            ->method('decorate')
            ->will(
                $this->returnCallback(
                    function (ParameterBag $params, $collection) {
                        return $collection;
                    }
                )
            );

        $element = $this->getMockForAbstractClass(ApiListableInterface::class);
        $element->method('getId')->willReturn(42);

        $collection = new ArrayCollection();
        $collection->add($element);

        $event = $this->getEvent($collection);


        $this->getListener()->onKernelView($event);

        $this->assertInstanceOf(CollectionRepresentation::class, $event->getControllerResult());
    }
}

class TestResource implements ApiListableInterface
{
    public function getId()
    {
        return 42;
    }
}
