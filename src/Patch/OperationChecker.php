<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

class OperationChecker implements OperationCheckerInterface
{

    /**
     * @param array $operation
     * @throws OperationInvalidException
     */
    public function validate(array $operation)
    {
        if (!array_key_exists('op', $operation)) {
            throw new OperationInvalidException(
                sprintf(
                    OperationInvalidException::MISSING_PROPERTY_MESSAGE,
                    'op'
                )
            );
        }
        if (!array_key_exists('path', $operation)) {
            throw new OperationInvalidException(
                sprintf(
                    OperationInvalidException::MISSING_PROPERTY_MESSAGE,
                    'path'
                )
            );
        }
    }
}
