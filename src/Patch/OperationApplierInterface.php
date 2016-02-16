<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\OperationInvalidException;

interface OperationApplierInterface
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param ValueInterface      $pathValue
     * @param ValueInterface|null $fromValue
     * @param mixed|null          $value
     * @param array               $parameters
     * @return
     * @throws InvalidPathException
     * @throws OperationInvalidException
     */
    public function apply(
        ValueInterface $pathValue,
        ValueInterface $fromValue = null,
        $value = null,
        array $parameters = []
    );
}
