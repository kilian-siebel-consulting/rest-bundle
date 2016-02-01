<?php

namespace Ibrows\RestBundle\Tests\Unit\Controller;


use FOS\RestBundle\Util\ExceptionWrapper;
use FOS\RestBundle\Util\MediaTypeNegotiatorInterface;
use FOS\RestBundle\View\ExceptionWrapperHandler;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Ibrows\RestBundle\Controller\ExceptionController;
use Ibrows\RestBundle\Exception\BadRequestConstraintException;
use Ibrows\RestBundle\Exception\FlattenException;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

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
        // believe it or not, if we don't set this it will cause
        // the exception_controller to pop output buffers. very funny.
        // this makes baby jesus cry.
        $request->headers->set('X-Php-Ob-Level', 100000);
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

    /**
     * @return ContainerInterface
     */
    private function getContainer($isDebug = true)
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);

        $forFormatNegotiator = $this->getMockForAbstractClass(MediaTypeNegotiatorInterface::class);

        $forFormatNegotiator
            ->method('getBestFormat')
            ->willReturn('json');
        
        $kernel = $this->getMockBuilder(Kernel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $kernel->method('isDebug')->willReturn($isDebug);
            
        $viewHandler = $this->getMockBuilder(ViewHandler::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $viewHandler
            ->method('handle')
            ->willReturnCallback(function(View $view, Request $request = null) {
                /** @var ExceptionWrapper $wrapped */
                $wrapped = $view->getData();
                $vars = [
                    'code' => $wrapped->getCode(),
                    'message' => $wrapped->getMessage(),
                    'errors' => $wrapped->getErrors()
                ];
                return new Response(json_encode($vars), $view->getStatusCode());
            });
        
        $wrapper = new ExceptionWrapperHandler();
        
        $container
            ->method('get')
            ->willReturnCallback(function($serviceId) use($forFormatNegotiator, $viewHandler, $kernel, $wrapper) {
                if($serviceId === 'fos_rest.exception_format_negotiator') {
                    return $forFormatNegotiator;
                } elseif($serviceId === 'fos_rest.view_handler') {
                    return $viewHandler;
                } elseif($serviceId === 'kernel') {
                    return $kernel;
                } elseif($serviceId === 'fos_rest.exception_handler') {
                    return $wrapper;
                }
                return null;
        });
        
        $container
            ->method('getParameter')
            ->willReturnCallback(function($parameterName) {
                if($parameterName === 'fos_rest.exception.codes' ||
                    $parameterName === 'fos_rest.exception.messages') {
                    return [];
                }
                return null;
            });
        
        return $container;
    }
    
    public function testWhenDebugEnabled()
    {
        $controller = new ExceptionController();
        $controller->setContainer($this->getContainer());
        
        $exception = $this->getException(403, "Du kommst hier ned rein");
        
        $response = $controller->showAction($this->getRequest(), $exception, null);
        
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertJson($response->getContent());
        
        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('errors', $data);
    }
    
    private function getDisplayableException($code, array $violations)
    {
        $violationList = new ConstraintViolationList();
        
        $props = [
            'code' => 'getCode',
            'message' => 'getMessage',
            'property_path' => 'getPropertyPath'
        ];

        foreach($violations as $violation) {
            $violationItem = $this->getMockForAbstractClass(ConstraintViolationInterface::class);

            foreach($props as $key => $propName) {
                $violationItem
                    ->method($propName)
                    ->willReturn($violation[$key]);
            }
            
            $violationList->add($violationItem);
        }
        
        
        return FlattenException::create(new BadRequestConstraintException($violationList, null, $code));
    }
    
    public function testWhenExceptionIsDisplayable()
    {
        $controller = new ExceptionController();
        $controller->setContainer($this->getContainer());

        $exception = $this->getDisplayableException(
            400,
            [
                [
                    'code'          => '123',
                    'message'       => 'This value should not be null.',
                    'property_path' => 'user',
                ]

            ]
        );

        $response = $controller->showAction($this->getRequest(), $exception, null);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('errors', $data);
        
        $this->assertEquals(400, $data['code']);
        $this->assertCount(1, $data['errors']);

        $this->assertArrayHasKey('violations', $data['errors']);
        
        $this->assertArrayHasKey('code', $data['errors']['violations'][0]);
        $this->assertArrayHasKey('message', $data['errors']['violations'][0]);
        $this->assertArrayHasKey('property_path', $data['errors']['violations'][0]);

        $this->assertEquals('123', $data['errors']['violations'][0]['code']);
        $this->assertEquals('This value should not be null.', $data['errors']['violations'][0]['message']);
        $this->assertEquals('/user', $data['errors']['violations'][0]['property_path']);        
        
    }
}
