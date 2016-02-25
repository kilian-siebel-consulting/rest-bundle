<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;

interface ValueConverterInterface
{
    /**
     * @param mixed          $value
     * @param ValueInterface $pathValue
     * @return mixed
     * @throws InvalidPathException
     */
    public function convert($value, ValueInterface $pathValue);
}
