<?php

namespace Ibrows\RestBundle\Tests\Unit\View;


use Ibrows\RestBundle\Exception\FlattenException;
use Ibrows\RestBundle\View\ExceptionWrapperHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionWrapperHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param boolean $debug
     * @return ExceptionWrapperHandler
     */
    private function getWrapperHandler($debug)
    {
        return new ExceptionWrapperHandler($debug);
    }

    private function getDataWithException()
    {
        return [
            'status_code' => 401,
            'message' => 'Unauthorized',
            'exception' => FlattenException::create(new HttpException(401, 'Unauthorized'))
        ];
    }
    
    private function getDataWithoutException() 
    {
        return [
            'status_code' => 201,
            'message' => 'Created',
        ];        
    }

    public function testInDebugModeWithoutException()
    {
        $handler = $this->getWrapperHandler(true);
        $wrapper = $handler->wrap($this->getDataWithoutException());

        $this->assertInstanceOf(\Ibrows\RestBundle\Util\ExceptionWrapper::class, $wrapper);
        $this->assertNull($wrapper->getException());
    }
    
    public function testInDebugModeWithException()
    {
        $handler = $this->getWrapperHandler(true);
        
        $wrapper = $handler->wrap($this->getDataWithException());
        
        $this->assertInstanceOf(\Ibrows\RestBundle\Util\ExceptionWrapper::class, $wrapper);
        $this->assertNotNull($wrapper->getException());
    }

    public function testInProdModeWithException()
    {
        $handler = $this->getWrapperHandler(false);

        $wrapper = $handler->wrap($this->getDataWithException());

        $this->assertInstanceOf(\FOS\RestBundle\Util\ExceptionWrapper::class, $wrapper);
        $this->assertNotInstanceOf(\Ibrows\RestBundle\Util\ExceptionWrapper::class, $wrapper);
    }
}
