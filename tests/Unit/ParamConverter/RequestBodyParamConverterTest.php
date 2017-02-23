<?php

namespace Ibrows\RestBundle\Tests\Unit\ParamConverter;

use FOS\RestBundle\Request\RequestBodyParamConverter as FOSRequestBodyParamConverter;
use Ibrows\RestBundle\ParamConverter\RequestBodyParamConverter;
use PHPUnit_Framework_MockObject_MockObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestBodyParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FOSRequestBodyParamConverter|PHPUnit_Framework_MockObject_MockObject
     */
    private $requestBodyConverter;

    /**
     * @var ConstraintViolationListInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $constraintViolations;

    public function setUp()
    {
        $this->requestBodyConverter = self::createMock(ParamConverterInterface::class);
        $this->constraintViolations = self::createMock(ConstraintViolationListInterface::class);
    }

    /**
     * @param array $configuration
     * @return RequestBodyParamConverter
     */
    private function getConverter(array $configuration)
    {
        $converter = new RequestBodyParamConverter(
            $this->requestBodyConverter,
            $configuration
        );

        return $converter;
    }


    private function getRequest()
    {
        $request = new Request(
            [],
            [],
            [
                'testValidationErrors' => $this->constraintViolations,
            ]
        );

        return $request;
    }

    /**
     * @return ParamConverter|PHPUnit_Framework_MockObject_MockObject
     */
    private function getConfiguration($failOnValidationError = true)
    {
        $configuration = self::createMock(ParamConverter::class);

        $configuration
            ->method('getOptions')
            ->willReturn(
                [
                    'fail_on_validation_error' => $failOnValidationError,
                ]
            );

        return $configuration;
    }

    public function testWithValidRequest()
    {
        $converter = $this->getConverter(
            [
                'fail_on_validation_error'   => true,
                'validation_errors_argument' => 'testValidationErrors',
            ]
        );

        $this->constraintViolations
            ->method('count')
            ->willReturn(0);

        $request = $this->getRequest();

        $configuration = $this->getConfiguration();

        $converter->apply($request, $configuration);

        $this->assertTrue($request->attributes->has('testValidationErrors'));
    }

    /**
     * @expectedException \Ibrows\RestBundle\Exception\BadRequestConstraintException
     */
    public function testWithFailingValidator()
    {
        $converter = $this->getConverter(
            [
                'fail_on_validation_error'   => true,
                'validation_errors_argument' => 'testValidationErrors',
            ]
        );

        $this->constraintViolations
            ->method('count')
            ->willReturn(7);

        $request = $this->getRequest();

        $configuration = $this->getConfiguration();

        $converter->apply($request, $configuration);
    }

    /**
     * @expectedException \Ibrows\RestBundle\Exception\BadRequestConstraintException
     */
    public function testWithGlobalDisableFailOnValidationShouldThrowExceptionWhenEnabledLocally()
    {
        $converter = $this->getConverter(
            [
                'fail_on_validation_error'   => false,
                'validation_errors_argument' => 'testValidationErrors',
            ]
        );

        $this->constraintViolations
            ->method('count')
            ->willReturn(7);

        $request = $this->getRequest();

        $configuration = $this->getConfiguration();

        $converter->apply($request, $configuration);
    }

    public function testWithGlobalEnabledFAilOnValidationShouldIgnoreErrorsWhenDisabledLocallly()
    {
        $converter = $this->getConverter(
            [
                'fail_on_validation_error'   => true,
                'validation_errors_argument' => 'testValidationErrors',
            ]
        );

        $this->constraintViolations
            ->method('count')
            ->willReturn(7);

        $request = $this->getRequest();

        $configuration = $this->getConfiguration(false);

        $converter->apply($request, $configuration);
    }

    public function testSupports()
    {
        $converter = $this->getConverter(
            [
                'fail_on_validation_error'   => true,
                'validation_errors_argument' => 'testValidationErrors',
            ]
        );

        $configuration = $this->getConfiguration();

        $this->requestBodyConverter
            ->method('supports')
            ->willReturn(true);

        $this->assertTrue($converter->supports($configuration));
    }
}
