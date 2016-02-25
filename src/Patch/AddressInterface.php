<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\OverridePathException;
use Ibrows\RestBundle\Patch\Exception\ResolvePathException;

interface AddressInterface
{
    /**
     * @return AddressInterface|null
     */
    public function parent();

    /**
     * Get the pointer
     *
     * @return PointerInterface
     */
    public function pointer();

    /**
     * Return the value
     *
     * @return mixed|null
     */
    public function & value();

    /**
     * @param PointerInterface $pointer
     * @return mixed|null
     * @throws InvalidPathException
     */
    public function & resolve(PointerInterface $pointer);

    /**
     * @param PointerInterface $pointer
     * @param mixed            $value
     * @throws InvalidPathException
     * @throws ResolvePathException
     * @throws OverridePathException
     */
    public function addElement(PointerInterface $pointer, $value);

    /**
     * @param PointerInterface $pointer
     * @param mixed            $value
     * @throws InvalidPathException
     * @throws ResolvePathException
     */
    public function modifyElement(PointerInterface $pointer, $value);

    /**
     * @param PointerInterface $pointer
     * @throws InvalidPathException
     * @throws ResolvePathException
     */
    public function removeElement(PointerInterface $pointer);
}
