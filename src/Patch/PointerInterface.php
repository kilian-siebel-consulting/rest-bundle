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
     * @return string
     */
    public function path();
}
