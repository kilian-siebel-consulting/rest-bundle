<?php
namespace Ibrows\RestBundle\Patch\AddressResolver;

use Ibrows\RestBundle\Patch\Address\HashSetAddress;
use Ibrows\RestBundle\Patch\AddressInterface;
use Ibrows\RestBundle\Patch\AddressResolverInterface;
use Ibrows\RestBundle\Patch\PointerInterface;

class HashSetAddressResolver implements AddressResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($value, PointerInterface $childPointer)
    {
        return is_array($value)
            ? 5
            : 0;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * {@inheritdoc}
     */
    public function resolve(& $value, PointerInterface $pointer, AddressInterface $parent = null, array $options = [])
    {
        return new HashSetAddress(
            $pointer,
            $value,
            $parent
        );
    }
}
