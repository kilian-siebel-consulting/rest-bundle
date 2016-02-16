<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;

interface JMSObjectAddressInterface extends AddressInterface
{
    /**
     * @param PointerInterface $pointer
     * @return array|null
     * @throws InvalidPathException
     */
    public function resolveType(PointerInterface $pointer);
}
