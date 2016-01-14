<?php
namespace Ibrows\RestBundle\Tests\Listener;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Ibrows\RestBundle\Listener\ExclusionPolicyResponseListener;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;

class ExclusionPolicyResponseListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HttpKernelInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kernel;

    /**
     * @var RequestContext|PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    public function setUp()
    {
        $this->kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
        $this->context = $this->getMockBuilder(RequestContext::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testDoNothingWhenDisabled()
    {
        $listener = $this->getListener(false);
        $event = $this->getEvent(false, '_expolicy', 'whatever', 'whatever');

        $listener->onKernelView($event);
    }


    public function testReturnPolicyNameWhenSet()
    {
        $listener = $this->getListener(true, '_expolicy');
        $event = $this->getEvent(true, '_expolicy', 'jedi-power', 'whatever');

        $listener->onKernelView($event);
    }

    public function testReturnDefaultValueWhenOmmited()
    {
        $listener = $this->getListener(true, '_expolicy');
        $event = $this->getEvent(true, '_expolicy', null, 'whatever');

        $listener->onKernelView($event);
    }

    public function testHandleWhenParamFetcherNotDefined()
    {
        $listener = $this->getListener(true, '_expolicy');

        $paramFetcher = $this->getMock(ParamFetcher::class, [], [], '', false);

        $paramFetcher->expects($this->once())
            ->method('get')
            ->with($this->equalTo('_expolicy'))
            ->willThrowException(new \InvalidArgumentException("Param not defined"));

        $request = new Request(
            [], [], [
                '_view'        => $this->getView(false, []),
                'paramFetcher' => $paramFetcher
            ]
        );

        $event = new GetResponseForControllerResultEvent(
            $this->kernel,
            $request,
            'whatever',
            'whatever-result'
        );

        $listener->onKernelView($event);
    }


    /**
     * @param bool $enabled
     * @param string $paramName
     * @return ExclusionPolicyResponseListener
     */
    private function getListener($enabled = true, $paramName = '_expolicy')
    {
        return new ExclusionPolicyResponseListener([
            'enabled'    => $enabled,
            'param_name' => $paramName
        ]);
    }

    /**
     * @return GetResponseForControllerResultEvent
     */
    private function getEvent($enabled, $paramName, $paramValue, $defaultValue)
    {
        $query = [];

        if (null !== $paramValue) {
            $query[$paramName] = $paramValue;
        }

        $view = $this->getView($enabled, $paramValue === null ? [$defaultValue] : [$paramValue]);
        $paramFetcher = $this->getParamFetcher($enabled, $paramName, $paramValue, $defaultValue);

        $request = new Request(
            $query, [], [
                '_view'        => $view,
                'paramFetcher' => $paramFetcher
            ]
        );

        return new GetResponseForControllerResultEvent(
            $this->kernel,
            $request,
            'whatever',
            'whatever-result'
        );
    }

    /**
     * @param $enabled
     * @param array $result
     * @return View
     */
    private function getView($enabled, array $result)
    {
        $view = $this->getMock(View::class, [], [], '', false);

        if (!$enabled) {
            $view->expects($this->never())->method('getSerializerGroups');
            $view->expects($this->never())->method('setSerializerGroups');
        } else {
            $view->expects($this->any())->method('getSerializerGroups')->willReturn([]);
            $view->expects($this->once())->method('setSerializerGroups')->with($this->equalTo($result));
        }

        return $view;
    }

    /**
     * @param boolean $enabled
     * @param string $paramName
     * @param string|null $paramValue
     * @param string $defaultValue
     * @return ParamFetcher
     */
    private function getParamFetcher($enabled, $paramName, $paramValue, $defaultValue)
    {
        $paramFetcher = $this->getMock(ParamFetcher::class, [], [], '', false);

        if (!$enabled) {
            $paramFetcher->expects($this->never())
                ->method('get');
        } else {
            $paramFetcher->expects($this->any())
                ->method('get')
                ->with($this->equalTo($paramName))
                ->willReturnCallback(
                    function () use ($paramValue, $defaultValue) {
                        if (null === $paramValue) {
                            return $defaultValue;
                        }
                        return $paramValue;
                    }
                );
        }

        return $paramFetcher;
    }
}