<?php
namespace Ibrows\RestBundle\Patch\Address;

use Ibrows\RestBundle\Patch\Exception\OverridePathException;
use Ibrows\RestBundle\Patch\Exception\ResolvePathException;
use Ibrows\RestBundle\Patch\PointerInterface;

class HashSetAddress extends AbstractAddress
{
    /**
     * {@inheritdoc}
     */
    public function & resolve(PointerInterface $pointer)
    {
        if (array_key_exists($pointer->lastToken(), $this->value())) {
            return $this->value()[$pointer->lastToken()];
        }
        $none = null;
        return $none;
    }

    /**
     * {@inheritdoc}
     */
    public function addElement(PointerInterface $pointer, $value)
    {
        if (array_key_exists($pointer->lastToken(), $this->value())) {
            throw new OverridePathException($this, $pointer);
        }
        $this->value()[$pointer->lastToken()] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyElement(PointerInterface $pointer, $value)
    {
        if (!array_key_exists($pointer->lastToken(), $this->value())) {
            throw new ResolvePathException($this, $pointer);
        }
        $this->value()[$pointer->lastToken()] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement(PointerInterface $pointer)
    {
        if (!array_key_exists($pointer->lastToken(), $this->value())) {
            throw new ResolvePathException($this, $pointer);
        }
        unset($this->value()[$pointer->lastToken()]);
    }
}
