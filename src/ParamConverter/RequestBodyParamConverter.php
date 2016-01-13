<?php
namespace Ibrows\RestBundle\ParamConverter;

use FOS\RestBundle\Request\RequestBodyParamConverter as BaseRequestBodyParamConverter;
use Ibrows\RestBundle\Exception\BadRequestConstraintException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RequestBodyParamConverter extends BaseRequestBodyParamConverter
{
    /**
     * @var array<string, mixed>
     */
    private $configuration;

    /**
     * {@inheritdoc}
     * @param array<string, mixed> $configuration
     */
    public function __construct(
        array $configuration,
        $serializer,
        $groups = null,
        $version = null,
        $validator = null,
        $validationErrorsArgument = null
    ) {
        /** @noinspection PhpParamsInspection */
        parent::__construct(
            $serializer,
            $groups,
            $version,
            $validator,
            $validationErrorsArgument
        );
        $this->configuration = $configuration;
    }


    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $value = parent::apply($request, $configuration);

        $failOnValidationError = isset($configuration->getOptions()['fail_on_validation_error'])
            ? $configuration->getOptions()['fail_on_validation_error']
            : $this->configuration['fail_on_validation_error'];

        if(
            $failOnValidationError &&
            count($request->attributes->get($this->validationErrorsArgument)) > 0
        ) {
            throw new BadRequestConstraintException($request->attributes->get($this->validationErrorsArgument));
        }

        return $value;
    }
}