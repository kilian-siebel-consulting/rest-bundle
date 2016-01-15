<?php
namespace Ibrows\RestBundle\Patch;

use ArrayIterator;
use Closure;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use ArrayAccess;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Class OperationCollection
 *
 * @package Ibrows\RestBundle\Patch
 *
 * @ExclusionPolicy("all")
 */
class OperationCollection implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     * @var OperationInterface[]
     * @Type("array<Ibrows\RestBundle\Patch\Operation>")
     * @Expose
     */
    private $operations = [];

    /**
     * Initializes a new ArrayCollection.
     *
     * @param OperationInterface[] $operations
     */
    public function __construct(array $operations = [])
    {
        $this->operations = $operations;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return $this->operations;
    }

    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return reset($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return end($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        if (!isset($this->operations[$key]) && !array_key_exists($key, $this->operations)) {
            return null;
        }

        $removed = $this->operations[$key];
        unset($this->operations[$key]);

        return $removed;
    }

    /**
     * {@inheritDoc}
     */
    public function removeElement(Operation $element)
    {
        $key = array_search($element, $this->operations, true);

        if ($key === false) {
            return false;
        }

        unset($this->operations[$key]);

        return true;
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof OperationInterface) {
            throw new InvalidArgumentException();
        }

        if (!isset($offset)) {
            return $this->add($value);
        }

        $this->set($offset, $value);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return isset($this->operations[$key]) || array_key_exists($key, $this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function contains(Operation $element)
    {
        return in_array($element, $this->operations, true);
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        foreach ($this->operations as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf(Operation $element)
    {
        return array_search($element, $this->operations, true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return isset($this->operations[$key]) ? $this->operations[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->operations[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->operations[] = $value;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return empty($this->operations);
    }

    /**
     * Required by interface IteratorAggregate.
     *
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->operations);
    }

    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        return new static(array_map($func, $this->operations));
    }

    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static(array_filter($this->operations, $p));
    }

    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p)
    {
        foreach ($this->operations as $key => $element) {
            if (!$p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        $matches = $noMatches = array();

        foreach ($this->operations as $key => $element) {
            if ($p($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }

        return array(new static($matches), new static($noMatches));
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . '@' . spl_object_hash($this);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->operations = array();
    }

    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        return array_slice($this->operations, $offset, $length, true);
    }
}