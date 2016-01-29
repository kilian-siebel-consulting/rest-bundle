<?php

namespace Ibrows\RestBundle\Tests\Listener;


use Ibrows\RestBundle\Exception\FlattenException;
use Ibrows\RestBundle\Listener\ExceptionListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return HttpKernelInterface
     */
    private function getKernel() 
    {
        $kernel = $this->getMockForAbstractClass(HttpKernelInterface::class);
        
        $kernel
            ->method('handle')
            ->willReturnCallback(function(Request $request) {
                $this->assertInstanceOf(FlattenException::class, $request->attributes->get('exception'));
                return new Response("test", 200);
            });
        
        return $kernel;
    }
    
    public function testCorrectFlatttenExceptionInstance()
    {
        $exceptionListener = new ExceptionListener("ibrows_rest.dummy.controller:schwurbelAction");

        $kernel = $this->getKernel();
        
        $request = new Request();
        $exception = new HttpException('test');
        
        $event = new GetResponseForExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );
        
        $exceptionListener->onKernelException($event);
    }
}
