<?php
namespace Ibrows\RestBundle\ParamConverter;

use Ibrows\RestBundle\Exception\BadRequestConstraintException;
use Ibrows\RestBundle\Patch\Executioner;
use Ibrows\RestBundle\Patch\Operation;
use Ibrows\RestBundle\Patch\OperationInterface;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PatchParamConverter extends ManipulationParamConverter
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Executioner
     */
    private $patchExecutioner;

    /**
     * PatchParamConverter constructor.
     *
     * @param array               $configuration
     * @param SerializerInterface $serializer
     * @param Executioner         $patchExecutioner
     */
    public function __construct(
        array $configuration,
        SerializerInterface $serializer,
        Executioner $patchExecutioner
    )
    {
        parent::__construct($configuration);
        $this->serializer = $serializer;
        $this->patchExecutioner = $patchExecutioner;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $object = $this->getObject($request, $configuration);

        $operations = $this->getOperations($request);

        $this->patchExecutioner->execute($object, $operations);

        $this->validate($object, $configuration, $request);

        return true;
    }

    /**
     * @param Request $request
     *
     * @return OperationInterface[]
     */
    private function getOperations(Request $request)
    {
        $bodyParserConfiguration = new ParamConverter([
            'class' => 'array<' . Operation::class . '>',
            'options' => [
                'source' => 'fos_rest.request_body',
            ],
            'name' => 'operations',
        ]);
        $converter = $this->getConverter($bodyParserConfiguration);

        $converter->apply($request, $bodyParserConfiguration);

        if(count($request->attributes->get('validationErrors')) > 0) {
            throw new BadRequestConstraintException($request->attributes->get('validationErrors'));
        }

        return $request->attributes->get('operations');
    }
}