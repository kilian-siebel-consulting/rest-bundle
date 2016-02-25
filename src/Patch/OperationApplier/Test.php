<?php
namespace Ibrows\RestBundle\Patch\OperationApplier;

use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;
use Ibrows\RestBundle\Patch\OperationApplierInterface;
use Ibrows\RestBundle\Patch\ValueInterface;

class Test implements OperationApplierInterface
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * {@inheritdoc}
     */
    public function apply(
        ValueInterface $pathValue,
        ValueInterface $fromValue = null,
        $value = null,
        array $parameters = []
    ) {
        if ((string)$pathValue->value() !== $value) {
            throw new OperationInvalidException(
                sprintf(
                    'Operation test failed. Expected: "%s", Actual: "%s"',
                    (string)$pathValue->value(),
                    (string)$value
                )
            );
        }
    }
}
