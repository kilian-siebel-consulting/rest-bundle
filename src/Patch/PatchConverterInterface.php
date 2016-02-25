<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

interface PatchConverterInterface
{
    /**
     * @param array $rawDiff
     * @return OperationInterface[]
     * @throws OperationInvalidException
     */
    public function convert(array $rawDiff);
}
