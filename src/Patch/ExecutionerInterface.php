<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;
use Ibrows\RestBundle\Patch\Exception\ResolvePathException;
use Ibrows\RestBundle\Patch\Exception\RootResolveException;
use InvalidArgumentException;

interface ExecutionerInterface
{
    /**
     * @param OperationInterface[] $operations
     * @param mixed                $object
     * @param mixed[]              $options
     * @return mixed
     * @throws OperationInvalidException
     * @throws InvalidPathException
     * @throws RootResolveException
     * @throws ResolvePathException
     * @throws InvalidArgumentException
     */
    public function execute(array $operations, $object, array $options = []);
}
