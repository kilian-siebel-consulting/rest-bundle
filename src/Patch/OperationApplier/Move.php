<?php
namespace Ibrows\RestBundle\Patch\OperationApplier;

use Ibrows\RestBundle\Patch\Address\ArrayItemAddress;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;
use Ibrows\RestBundle\Patch\OperationApplierInterface;
use Ibrows\RestBundle\Patch\ValueInterface;

class Move implements OperationApplierInterface
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
        if (!$fromValue instanceof ValueInterface) {
            throw new OperationInvalidException(
                sprintf(
                    OperationInvalidException::MISSING_SPECIALISED_PROPERTY_MESSAGE,
                    'from',
                    'move'
                )
            );
        }

        $tempValue = $fromValue->value();
        $fromValue->remove();
        $pathValue->add($tempValue);
    }
}
