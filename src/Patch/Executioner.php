<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

class Executioner implements ExecutionerInterface
{
    /**
     * @var ValueConverterInterface|null
     */
    private $valueConverter = null;

    /**
     * @var OperationApplierInterface[][]|int[][]
     */
    private $operationAppliers = [];

    /**
     * {@inheritdoc}
     */
    public function execute(array $operations, $object, array $options = [])
    {
        array_walk(
            $operations,
            function (OperationInterface $operation) use (& $object, $options) {
                $operationApplier = $this->getOperationApplier($operation->operation());
                $pathValue = $operation->pathPointer()->resolve($object, $options);

                $fromValue = null;
                if ($operation->fromPointer() !== null) {
                    $fromValue = $operation->fromPointer()->resolve($object, $options);
                }

                $value = $this->convertValue($operation->value(), $pathValue);

                $operationApplier->apply($pathValue, $fromValue, $value, $operation->parameters());
            }
        );

        return $object;
    }

    /**
     * @param string                    $operation
     * @param OperationApplierInterface $operationApplier
     * @param int                       $priority
     */
    public function addOperationApplier($operation, OperationApplierInterface $operationApplier, $priority)
    {
        if (!array_key_exists($operation, $this->operationAppliers) ||
            $this->operationAppliers[$operation]['priority'] < $priority
        ) {
            $this->operationAppliers[$operation] = [
                'operationApplier' => $operationApplier,
                'priority'         => (int)$priority,
            ];
        }
    }

    /**
     * @param ValueConverterInterface $valueConverter
     */
    public function setValueConverter($valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * @param mixed|null     $value
     * @param ValueInterface $pathValue
     * @return mixed
     * @throws InvalidPathException
     */
    private function convertValue($value, ValueInterface $pathValue)
    {
        if ($value === null ||
            $this->valueConverter === null
        ) {
            return $value;
        }

        return $this->valueConverter->convert($value, $pathValue);
    }

    /**
     * @param string $operation
     * @return OperationApplierInterface
     * @throws OperationInvalidException
     */
    private function getOperationApplier($operation)
    {
        if (!array_key_exists($operation, $this->operationAppliers)) {
            throw new OperationInvalidException(
                sprintf(
                    OperationInvalidException::INVALID_OPERATION,
                    $operation
                )
            );
        }
        return $this->operationAppliers[$operation]['operationApplier'];
    }
}
