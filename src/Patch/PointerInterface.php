<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;

interface PointerInterface
{
    /**
     * @return string[]
     * @throws InvalidPathException
     */
    public function tokens();

    /**
     * @return string
     * @throws InvalidPathException
     */
    public function lastToken();

    /**
     * @param mixed   $object
     * @param mixed[] $options
     * @return ValueInterface
     * @throws InvalidPathException
     */
    public function resolve(& $object, array $options = []);

    /**
     * @return string
     */
    public function path();
}
