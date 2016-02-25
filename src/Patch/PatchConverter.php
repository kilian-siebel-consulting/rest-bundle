<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

class PatchConverter implements PatchConverterInterface
{
    /**
     * @var PointerFactoryInterface
     */
    private $pointerFactory;

    /**
     * @var OperationFactoryInterface
     */
    private $operationFactory;

    /**
     * PatchConverter constructor.
     * @param PointerFactoryInterface   $pointerFactory
     * @param OperationFactoryInterface $operationFactory
     */
    public function __construct(
        PointerFactoryInterface $pointerFactory,
        OperationFactoryInterface $operationFactory
    ) {
        $this->pointerFactory = $pointerFactory;
        $this->operationFactory = $operationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $rawPatch)
    {
        $patch = [];

        while($rawOperation = array_shift($rawPatch)) {
            $this->validateOperation($rawOperation);

            $pointer = $this->pointerFactory->createFromPath($rawOperation['path']);

            $value = array_key_exists('value', $rawOperation)
                ? $rawOperation['value']
                : null;

            $fromPointer = array_key_exists('from', $rawOperation)
                ? $this->pointerFactory->createFromPath($rawOperation['from'])
                : null;

            $parameters = array_diff_key($rawOperation, [
                'op' => true,
                'path' => true,
                'value' => true,
                'from' => true,
            ]);

            array_push(
                $patch,
                $this->operationFactory->create(
                    $rawOperation['op'],
                    $pointer,
                    $fromPointer,
                    $value,
                    $parameters
                )
            );
        }

        return $patch;
    }

    /**
     * @param array $rawOperation
     * @throws OperationInvalidException
     */
    private function validateOperation($rawOperation)
    {
        if (!array_key_exists('op', $rawOperation)) {
            throw new OperationInvalidException(
                sprintf(
                    OperationInvalidException::MISSING_PROPERTY_MESSAGE,
                    'op'
                )
            );
        }
        if (!array_key_exists('path', $rawOperation)) {
            throw new OperationInvalidException(
                sprintf(
                    OperationInvalidException::MISSING_PROPERTY_MESSAGE,
                    'path'
                )
            );
        }
    }
}
