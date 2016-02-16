<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

interface OperationCheckerInterface
{
    /**
     * @param array $operation
     * @throws OperationInvalidException
     */
    public function validate(array $operation);
}
