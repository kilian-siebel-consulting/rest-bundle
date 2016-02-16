<?php
namespace Ibrows\RestBundle\Patch\Address;

use Ibrows\RestBundle\Patch\Exception\ResolvePathException;
use Ibrows\RestBundle\Patch\PointerInterface;

class ListAddress extends AbstractAddress
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
        if ($pointer->lastToken() === '-') {
            $this->appendElement($value);
            return;
        } elseif ($pointer->lastToken() === '0') {
            $this->prependElement($value);
            return;
        }

        $before = array_slice($this->value(), 0, $pointer->lastToken());
        $after = array_slice($this->value(), $pointer->lastToken());
        $this->value = array_merge(
            $before,
            [$value],
            $after
        );
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
        $this->value = array_values($this->value());
    }

    /**
     * @param mixed $value
     */
    public function appendElement($value)
    {
        $this->value()[] = $value;
    }

    /**
     * @param mixed $value
     */
    public function prependElement($value)
    {
        array_unshift($this->value(), $value);
    }
}
