<?php
namespace Ibrows\RestBundle\Patch\OperationApplier;

use Ibrows\RestBundle\Patch\OperationApplierInterface;
use Ibrows\RestBundle\Patch\ValueInterface;

class Replace implements OperationApplierInterface
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

        $pathValue->modify($value);
    }
}
