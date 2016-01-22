<?php
namespace Ibrows\RestBundle\ParamConverter;

use FOS\RestBundle\Request\RequestBodyParamConverter as BaseRequestBodyParamConverter;
use Ibrows\RestBundle\Exception\BadRequestConstraintException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestBodyParamConverter implements ParamConverterInterface
{
    /**
     * @var BaseRequestBodyParamConverter
     */
    private $decoratedRequestBodyParamConverter;


    /**
     * @var string
     */
    private $validationErrorsArgument;


    public function __construct(BaseRequestBodyParamConverter $decoratedRequestBodyParamConverter, $validationErrorsArgument)
    {
        $this->decoratedRequestBodyParamConverter = $decoratedRequestBodyParamConverter;
        $this->validationErrorsArgument = $validationErrorsArgument;
    }


    public function apply(Request $request, ParamConverter $configuration)
    {
        $value = $this->decoratedRequestBodyParamConverter->apply($request, $configuration);
        $validationErrors = $request->attributes->get($this->validationErrorsArgument);
        if (count($validationErrors) > 0) {
            throw new BadRequestConstraintException($validationErrors);
        }
        return $value;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $this->decoratedRequestBodyParamConverter->supports($configuration);
    }


}