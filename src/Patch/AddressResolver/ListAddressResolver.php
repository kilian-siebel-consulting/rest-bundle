<?php
namespace Ibrows\RestBundle\Patch\AddressResolver;

use Ibrows\RestBundle\Patch\Address\ListAddress;
use Ibrows\RestBundle\Patch\AddressInterface;
use Ibrows\RestBundle\Patch\AddressResolverInterface;
use Ibrows\RestBundle\Patch\PointerInterface;

class ListAddressResolver implements AddressResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($value, PointerInterface $childPointer)
    {
        if ($childPointer->lastToken() !== '-' &&
            !is_numeric($childPointer->lastToken())
        ) {
            return 0;
        }

        if (!is_array($value)) {
            return 0;
        }

        /** @noinspection ReferenceMismatchInspection */
        $nonNumericKeys = array_filter(
            array_keys($value),
            function ($key) {
                return !is_numeric($key);
            }
        );

        return count($nonNumericKeys) < 1
            ? 10
            : 0;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * {@inheritdoc}
     */
    public function resolve(& $value, PointerInterface $pointer, AddressInterface $parent = null, array $options = [])
    {
        return new ListAddress(
            $pointer,
            $value,
            $parent
        );
    }
}
