<?php
namespace Ibrows\RestBundle\Patch;

class Value implements ValueInterface
{
    /**
     * @var AddressInterface
     */
    private $parent;

    /**
     * @var PointerInterface
     */
    private $pointer;

    /**
     * Value constructor.
     * @param AddressInterface $parent
     * @param PointerInterface $pointer
     */
    public function __construct(
        AddressInterface $parent,
        PointerInterface $pointer
    ) {
        $this->parent = $parent;
        $this->pointer = $pointer;
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
    public function remove()
    {
        $this->parent->removeElement($this->pointer);
    }

    /**
     * {@inheritdoc}
     */
    public function add($value)
    {
        $this->parent->addElement($this->pointer, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function modify($value)
    {
        $this->parent->modifyElement($this->pointer, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function & value()
    {
        return $this->parent->resolve($this->pointer);
    }

    /**
     * {@inheritdoc}
     */
    public function type()
    {
        if (!$this->parent() instanceof JMSObjectAddressInterface) {
            return null;
        }
        /** @var JMSObjectAddressInterface $parent */
        $parent = $this->parent();
        return $parent->resolveType($this->pointer);
    }
}
