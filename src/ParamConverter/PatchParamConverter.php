<?php
namespace Ibrows\RestBundle\ParamConverter;

use Ibrows\JsonPatch\Exception\InvalidPathException;
use Ibrows\JsonPatch\Exception\OperationInvalidException;
use Ibrows\JsonPatch\ExecutionerInterface;
use Ibrows\JsonPatch\OperationInterface;
use Ibrows\JsonPatch\PatchConverterInterface;
use Ibrows\RestBundle\Patch\OperationAuthorizationCheckerInterface;
use JMS\Serializer\DeserializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PatchParamConverter extends ManipulationParamConverter
{
    /**
     * @var PatchConverterInterface
     */
    private $patchConverter;

    /**
     * @var ExecutionerInterface
     */
    private $patchExecutioner;

    /**
     * @var OperationAuthorizationCheckerInterface
     */
    private $operationAuthorizationChecker;

    /**
     * PatchParamConverter constructor.
     * @param array $configuration
     * @param PatchConverterInterface $patchConverter
     * @param ExecutionerInterface $patchExecutioner
     * @param OperationAuthorizationCheckerInterface $operationAuthorizationChecker
     */
    public function __construct(
        array $configuration,
        PatchConverterInterface $patchConverter,
        ExecutionerInterface $patchExecutioner,
        OperationAuthorizationCheckerInterface $operationAuthorizationChecker
    ) {
        parent::__construct($configuration);
        $this->patchConverter = $patchConverter;
        $this->patchExecutioner = $patchExecutioner;
        $this->operationAuthorizationChecker = $operationAuthorizationChecker;
    }

    /**
     * {@inheritdoc}
     * @throws BadRequestHttpException
     * @throws AccessDeniedHttpException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $this->checkRequest($request);

        $operations = $this->loadOperations($request);
        $context = $this->loadDeserializationContext($configuration);
        $subject = $this->getObject($request, $configuration);

        $this->checkPermissions($operations, $subject);

        $subject = $this->applyPatch(
            $subject,
            $operations,
            [
                'jms_context' => $context,
            ]
        );

        $this->validate($subject, $configuration, $request);

        return false;
    }

    /**
     * @param OperationInterface[] $operations
     * @param       $subject
     */
    private function checkPermissions(array $operations, $subject)
    {

        if (!$this->operationAuthorizationChecker->isGranted($operations, $subject)) {
            throw new AccessDeniedHttpException('Access Denied for a single patch Operation');
        }
    }

    /**
     * @param Request $request
     * @throws BadRequestHttpException
     */
    private function checkRequest(Request $request)
    {
        if ($request->getContentType() !== 'json') {
            throw new BadRequestHttpException('Content Type must be json.');
        }
    }

    /**
     * @param Request $request
     * @return OperationInterface[]
     * @throws BadRequestHttpException
     */
    private function loadOperations(Request $request)
    {
        try {
            return $this->patchConverter->convert($request->request->all());
        } catch (OperationInvalidException $exception) {
            throw new BadRequestHttpException(
                $exception->getMessage(),
                $exception
            );
        }
    }

    /**
     * @param ParamConverter $configuration
     * @return DeserializationContext
     */
    private function loadDeserializationContext(ParamConverter $configuration)
    {
        $options = (array)$configuration->getOptions();

        $context = DeserializationContext::create();

        if (array_key_exists('deserializationContext', $options) &&
            is_array($options['deserializationContext'])
        ) {
            if (array_key_exists('groups', $options['deserializationContext'])) {
                $context->setGroups($options['deserializationContext']['groups']);
            }
            if (array_key_exists('version', $options['deserializationContext'])) {
                $context->setVersion($options['deserializationContext']['version']);
            }
        }

        return $context;
    }

    /**
     * @param mixed $subject
     * @param OperationInterface[] $operations
     * @param mixed[] $options
     * @return mixed
     * @throws BadRequestHttpException
     */
    private function applyPatch($subject, array $operations, array $options)
    {
        try {
            return $this->patchExecutioner->execute($operations, $subject, $options);
        } catch (InvalidPathException $exception) {
            throw new BadRequestHttpException(
                $exception->getMessage(),
                $exception
            );
        } catch (OperationInvalidException $exception) {
            throw new BadRequestHttpException(
                $exception->getMessage(),
                $exception
            );
        }
    }
}
