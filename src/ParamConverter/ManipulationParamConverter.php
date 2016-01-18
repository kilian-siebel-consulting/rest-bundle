<?php
namespace Ibrows\RestBundle\ParamConverter;

use Ibrows\RestBundle\Exception\BadRequestConstraintException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ManipulationParamConverter implements ParamConverterInterface
{
    /**
     * @var array<string, mixed>
     */
    private $configuration;

    /**
     * @var array<string, ParamConverterInterface>
     */
    private $paramConverters;

    /**
     * @var ValidatorInterface|null
     */
    private $validator;

    /**
     * ManipulationParamConverter constructor.
     *
     * @param array              $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->paramConverters = [];
        $this->configuration = $configuration;
        $this->validator = null;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        try {
            $converter = $this->getConverter($configuration);

            return $converter->supports($configuration);
        } catch(InvalidConfigurationException $exception) {
            return false;
        }
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return mixed
     */
    protected function getObject(Request $request, ParamConverter $configuration)
    {
        $this->getConverter($configuration)->apply($request, $configuration);

        $parameterName = $configuration->getName();

        return $request->attributes->get($parameterName);
    }

    /**
     * @param                $object
     * @param ParamConverter $configuration
     * @param Request        $request
     */
    protected function validate($object, ParamConverter $configuration, Request $request)
    {
        if($this->validator === null) {
            return;
        }

        $validatorOptions = $this->getValidatorOptions($configuration->getOptions());
        $errors = $this->validator->validate($object, null, $validatorOptions['groups']);

        $request->attributes->set(
            'validationErrors',
            $errors
        );

        if(
            $this->shouldFail($configuration) &&
            count($errors) > 0
        ) {
            throw new BadRequestConstraintException($errors);
        }
    }

    /**
     * @param $configuration
     * @return bool
     */
    protected function shouldFail(ParamConverter $configuration)
    {
        return isset($configuration->getOptions()['fail_on_validation_error'])
            ? $configuration->getOptions()['fail_on_validation_error']
            : $this->configuration['fail_on_validation_error'];
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getValidatorOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'groups' => null,
            'traverse' => false,
            'deep' => false,
        ));

        return $resolver->resolve(isset($options['validator']) ? $options['validator'] : array());
    }


    /**
     * @param ParamConverter $configuration
     *
     * @return ParamConverterInterface
     */
    protected function getConverter(ParamConverter $configuration)
    {
        if(!isset($configuration->getOptions()['source'])) {
            throw new InvalidConfigurationException('The option "source" has to be provided for the ParamConverter.');
        }

        $name = $configuration->getOptions()['source'];

        if(!isset($this->paramConverters[$name])) {
            throw new InvalidConfigurationException('The ParamConverter ' . $name . ' does not exist.');
        }

        return $this->paramConverters[$name];
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string                  $name
     * @param ParamConverterInterface $paramConverter
     */
    public function addConverter($name, ParamConverterInterface $paramConverter)
    {
        $this->paramConverters[$name] = $paramConverter;
    }
}