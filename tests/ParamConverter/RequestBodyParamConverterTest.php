<?php
/**
 * Created by PhpStorm.
 * User: stefanvetsch
 * Date: 18.01.16
 * Time: 15:29
 */

namespace Ibrows\RestBundle\Tests\ParamConverter;


use Ibrows\RestBundle\ParamConverter\RequestBodyParamConverter;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyParamConverterTest extends \PHPUnit_Framework_TestCase
{
    
    private function getConverter($configuraiton = [], $failingValidator = false)
    {
        $validator = $this->getMockForAbstractClass(ValidatorInterface::class);

        $constraintViolationsListInterface = $this->getMockForAbstractClass(ConstraintViolationListInterface::class);

        $constraintViolationsListInterface
            ->method('count')
            ->willReturn((int)$failingValidator);

        $validator
            ->expects($this->any())
            ->method('validate')
            ->willReturn($constraintViolationsListInterface);

        $serializer = $this->getMockForAbstractClass(SerializerInterface::class);
        
        $converter = new RequestBodyParamConverter($configuraiton, $serializer, null, null, $validator, 'testValidationErrors');
        
        return $converter;
    }
    
    
    private function getRequest()
    {
        $request = new Request();
        
        return $request;
    }
    
    private function getConfiguration()
    {
        $configuration = $this->getMockBuilder(ParamConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration
            ->method('getOptions')
            ->willReturn([
                'fail_on_validation_error' => true,
            ]);
        
        return $configuration;
    }
    
    public function testWithValidRequest()
    {
        $converter = $this->getConverter();

        $request = $this->getRequest();

        $configuration = $this->getConfiguration();
        
        $converter->apply($request, $configuration);
        
        $this->assertTrue($request->attributes->has('testValidationErrors'));
        $this->assertEmpty($request->attributes->get('testValidationErrors'));
    }

    /**
     * @expectedException \Ibrows\RestBundle\Exception\BadRequestConstraintException
     */
    public function testWithFailingValidator()
    {
        $converter = $this->getConverter([], true);

        $request = $this->getRequest();

        $configuration = $this->getConfiguration();

        $converter->apply($request, $configuration);
    }
}
