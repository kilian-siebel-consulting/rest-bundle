<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;

interface AddressResolverInterface
{
    /**
     * @param mixed            $value
     * @param PointerInterface $childPointer
     * @return int weight
     * @throws InvalidPathException
     */
    public function supports($value, PointerInterface $childPointer);

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param mixed                 $value
     * @param PointerInterface      $pointer
     * @param AddressInterface|null $parent
     * @param mixed[]               $options
     * @return AddressInterface
     */
    public function resolve(& $value, PointerInterface $pointer, AddressInterface $parent = null, array $options = []);
}
