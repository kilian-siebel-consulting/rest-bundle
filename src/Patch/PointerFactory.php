<?php
namespace Ibrows\RestBundle\Patch;

use Closure;
use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\ResolvePathException;
use Ibrows\RestBundle\Patch\Exception\RootResolveException;
use InvalidArgumentException;

class PointerFactory implements PointerFactoryInterface
{
    /**
     * @var AddressLookupInterface
     */
    private $addressLookup;

    /**
     * @var TokenEscapeInterface
     */
    private $tokenEscaper;

    /**
     * PointerFactory constructor.
     * @param AddressLookupInterface $addressLookup
     * @param TokenEscapeInterface   $tokenEscaper
     */
    public function __construct(
        AddressLookupInterface $addressLookup,
        TokenEscapeInterface $tokenEscaper
    ) {
        $this->addressLookup = $addressLookup;
        $this->tokenEscaper = $tokenEscaper;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromPath($path)
    {
        return Pointer::fromPath(
            $path,
            $this->getAddressLookupClosure(),
            $this->tokenEscaper
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createFromTokens(array $tokens)
    {
        return Pointer::fromTokens(
            $tokens,
            $this->getAddressLookupClosure(),
            $this->tokenEscaper
        );
    }

    /**
     * @return Closure
     */
    private function getAddressLookupClosure()
    {
        /**
         * @param PointerInterface $pointer
         * @param mixed            $object
         * @param mixed[]          $options
         * @return AddressInterface
         * @throws InvalidPathException
         * @throws RootResolveException
         * @throws ResolvePathException
         * @throws InvalidArgumentException
         */
        return function (PointerInterface $pointer, & $object, array $options = []) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            return $this->addressLookup->lookup(
                $this,
                $pointer,
                $object,
                $options
            );
        };
    }
}
