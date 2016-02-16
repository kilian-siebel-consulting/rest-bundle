<?php
namespace Ibrows\RestBundle\Patch\Address;

use Ibrows\RestBundle\Patch\AddressInterface;
use Ibrows\RestBundle\Patch\PointerInterface;

abstract class AbstractAddress implements AddressInterface
{
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @var AddressInterface|null
     */
    private $parent;
    /**
     * @var PointerInterface
     */
    private $pointer;

    /**
     * AbstractAddress constructor.
     * @param PointerInterface      $pointer
     * @param mixed                 $value
     * @param AddressInterface|null $parent
     */
    public function __construct(
        PointerInterface $pointer,
        & $value,
        AddressInterface $parent = null
    ) {
        $this->parent = $parent;
        $this->pointer = $pointer;
        $this->value =& $value;
    }

    /**
     * {@inheritdoc}
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function pointer()
    {
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function & value()
    {
        return $this->value;
    }
}
