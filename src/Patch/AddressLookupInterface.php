<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\ResolvePathException;
use Ibrows\RestBundle\Patch\Exception\RootResolveException;
use InvalidArgumentException;

interface AddressLookupInterface
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param PointerInterface        $pointer
     * @param mixed                   $object
     * @param mixed[]                 $options
     * @return ValueInterface
     * @throws InvalidPathException
     * @throws RootResolveException
     * @throws ResolvePathException
     * @throws InvalidArgumentException
     */
    public function lookup(
        PointerInterface $pointer,
        & $object,
        array $options = []
    );
}
