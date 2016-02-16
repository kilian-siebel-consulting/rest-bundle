<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;

interface ValueInterface
{
    /**
     * @return AddressInterface
     */
    public function parent();

    /**
     * @return PointerInterface
     */
    public function pointer();

    /**
     * @throws InvalidPathException
     */
    public function remove();

    /**
     * @param mixed $value
     * @throws InvalidPathException
     */
    public function add($value);

    /**
     * @param mixed $value
     * @throws InvalidPathException
     */
    public function modify($value);

    /**
     * @return mixed
     * @throws InvalidPathException
     */
    public function & value();

    /**
     * @return array|null
     * @throws InvalidPathException
     */
    public function type();
}
