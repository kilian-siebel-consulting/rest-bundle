<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

interface ExecutionerInterface
{
    /**
     * @param OperationInterface[] $operations
     * @param mixed                $object
     * @param mixed[]              $options
     * @return mixed
     * @throws OperationInvalidException
     * @throws InvalidPathException
     */
    public function execute(array $operations, $object, array $options = []);
}
