<?php

namespace Ibrows\RestBundle\Tests\Controller;


use Ibrows\RestBundle\Controller\ExceptionController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        $serializer = $this->getMockForAbstractClass(SerializerInterface::class);
        $serializer
            ->method('serialize')
            ->willReturnCallback(function($object, $format, $context = null) {
                return json_encode($object);
            });
        
        
        return $serializer;
    }

    /**
     * @return Request
     */
    public function getRequest() 
    {
        $request = new Request([], [], [ '_format' => 'json']);
        return $request;
    }

    /**
     * @param $code
     * @param $message
     * @return FlattenException
     */
    public function getException($code, $message)
    {
        $inner = new HttpException($code, $message);
        $exception = FlattenException::create($inner, $code);
        return $exception;
    }
    
    public function testWhenDebugEnabled()
    {
        $controller = new ExceptionController(
            $this->getSerializer(),
            true
        );
        
        $exception = $this->getException(403, "Du kommst hier ned rein");
        
        $response = $controller->showAction($this->getRequest(), $exception, null);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertJson($response->getContent());
        
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('code', $data['error']);
        $this->assertArrayHasKey('message', $data['error']);
        $this->assertArrayHasKey('exception', $data['error']);
    }
    
    public function testWhenDebugNotEnabled()
    {
        $controller = new ExceptionController(
            $this->getSerializer(),
            false
        );

        $exception = $this->getException(403, "Du kommst hier ned rein");

        $response = $controller->showAction($this->getRequest(), $exception, null);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('error', $data);
        $this->assertArrayNotHasKey('exception', $data['error']);
    }
}
