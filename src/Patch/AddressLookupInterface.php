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
     * @param PointerFactoryInterface $pointerFactory
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
        PointerFactoryInterface $pointerFactory,
        PointerInterface $pointer,
        & $object,
        array $options = []
    );
}
