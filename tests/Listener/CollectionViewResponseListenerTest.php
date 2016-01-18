<?php
/**
 * Created by PhpStorm.
 * User: fabs
 * Date: 1/14/16
 * Time: 3:24 PM
 */

namespace Ibrows\RestBundle\Tests\Listener;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Hateoas\Representation\PaginatedRepresentation;
use Ibrows\RestBundle\Listener\AbstractCollectionDecorationListener;
use Ibrows\RestBundle\Listener\Decoration\CollectionDecorationListener;
use Ibrows\RestBundle\Listener\Decoration\LastIdDecorationListener;
use Ibrows\RestBundle\Listener\Decoration\OffsetDecorationListener;
use Ibrows\RestBundle\Listener\Decoration\PaginatedDecorationListener;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\LastIdRepresentation;
use Ibrows\RestBundle\Representation\OffsetRepresentation;
use Ibrows\RestBundle\Annotation\View as IbrowsView;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CollectionViewResponseListenerTest extends AbstractDecorationTest
{

    public function testSerialisationGroups()
    {
        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 1, 'offsetId' => 1, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $view = new View(array('serializerGroups' => array('helloworld')));

        $event = $this->getEvent($paramFetcher, array($listEntity), $view);
        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);
        $listener->onKernelView($event);

        $groups = $view->getSerializerGroups();
        $this->assertTrue( count($groups ) > 0);
        $this->assertTrue( in_array("hateoas_list", $groups ));
        $this->assertTrue( in_array("helloworld", $groups ));
    }

    public function testParams()
    {
        $listEntity = $this->getMockForAbstractClass(ApiListableInterface::class);
        $listEntity->method('getId')->willReturn(42);

        $paramFetcher = $this->getMockForAbstractClass(ParamFetcherInterface::class);
        $paramFetcher->method('all')->willReturn(array('limit' => 1, 'offsetId' => $listEntity, 'sortBy' => 'id', 'sortDir' => 'ASC'));
        $paramFetcher->method('get')->willThrowException(new \InvalidArgumentException());

        $view = new IbrowsView(array('serializerGroups' => array('helloworld')));
        $view->setRouteParams(array('offsetId', 'test'));

        $request = new Request(array(), array(), array('_view' => $view, 'paramFetcher' => $paramFetcher, '_route' => 'foo', 'test' => 'JEP'));

        $event = $this->getEvent($paramFetcher, array($listEntity), $view, $request);
        /** @var LastIdDecorationListener $listener */
        $listener = $this->setupDecoration(LastIdDecorationListener::class, $event);
        $listener->onKernelView($event);


        $parameters = $event->getControllerResult()->getParameters();

        $this->assertTrue(array_key_exists('sortBy', $parameters ));
        $this->assertEquals('id', $parameters['sortBy'] );
        $this->assertEquals(42, $parameters['offsetId'] );
    }

}