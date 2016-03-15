<?php
namespace Ibrows\RestBundle\Patch\Address;

use Ibrows\RestBundle\Exception\InvalidValueException;
use Ibrows\RestBundle\Patch\AddressInterface;
use Ibrows\RestBundle\Patch\Exception\InvalidPathException;
use Ibrows\RestBundle\Patch\Exception\OverridePathException;
use Ibrows\RestBundle\Patch\Exception\PropertyNullPathException;
use Ibrows\RestBundle\Patch\Exception\ResolvePathException;
use Ibrows\RestBundle\Patch\JMSObjectAddressInterface;
use Ibrows\RestBundle\Patch\PointerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\PropertyMetadata as JMSPropertyMetadata;
use Metadata\ClassMetadata;
use Metadata\PropertyMetadata;

class ObjectAddress extends AbstractAddress implements JMSObjectAddressInterface
{
    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var Context
     */
    private $context;

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param ClassMetadata              $classMetadata
     * @param ExclusionStrategyInterface $exclusionStrategy
     * @param Context                    $context
     * {@inheritdoc}
     */
    public function __construct(
        PointerInterface $pointer,
        & $value,
        ClassMetadata $classMetadata,
        Context $context = null,
        AddressInterface $parent = null
    ) {
        parent::__construct($pointer, $value, $parent);
        $this->classMetadata = $classMetadata;
        if (!$context) {
            $context = DeserializationContext::create();
        }
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function & resolve(PointerInterface $pointer)
    {
        $property = $this->getProperty($pointer);
        if ($property !== null) {
            $value = $property->getValue($this->value());
            return $value;
        }
        $none = null;
        return $none;
    }

    /**
     * {@inheritdoc}
     */
    public function addElement(PointerInterface $pointer, $value)
    {
        $property = $this->getProperty($pointer);
        if ($property === null) {
            throw new ResolvePathException($this, $pointer);
        } elseif ($property->getValue($this->value()) !== null) {
            throw new OverridePathException($this, $pointer);
        }

        try {
            $property->setValue($this->value(), $value);
        }catch(InvalidArgumentException $e){
            throw new InvalidValueException($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modifyElement(PointerInterface $pointer, $value)
    {
        $property = $this->getProperty($pointer);
        if ($property === null) {
            throw new ResolvePathException($this, $pointer);
        }
        if ($property->getValue($this->value())  === null) {
            throw new PropertyNullPathException($this, $pointer);
        }

        try {
            $property->setValue($this->value(), $value);
        }catch(InvalidArgumentException $e){
            throw new InvalidValueException($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement(PointerInterface $pointer)
    {
        $property = $this->getProperty($pointer);
        if ($property === null) {
            throw new ResolvePathException($this, $pointer);
        }
        if ($property->getValue($this->value())  === null) {
            throw new PropertyNullPathException($this, $pointer);
        }

        $property->setValue($this->value(), null);
    }

    /**
     * @param PointerInterface $pointer
     * @return PropertyMetadata|null
     * @throws InvalidPathException
     */
    private function getProperty(PointerInterface $pointer)
    {
        /** @var PropertyMetadata[] $matchingProperties */
        $matchingProperties = array_filter(
            $this->classMetadata->propertyMetadata,
            function (PropertyMetadata $propertyMetadata) use ($pointer) {
                $name = $propertyMetadata->name;
                if ($propertyMetadata instanceof JMSPropertyMetadata &&
                    $propertyMetadata->serializedName
                ) {
                    $name = $propertyMetadata->serializedName;
                }

                if ($propertyMetadata->readOnly) {
                    return false;
                }

                if ($name !== $pointer->lastToken()) {
                    return false;
                }

                $exclusionStrategy = $this->context->getExclusionStrategy();
                /** @noinspection IfReturnReturnSimplificationInspection */
                if ($exclusionStrategy &&
                    $exclusionStrategy->shouldSkipProperty($propertyMetadata, $this->context)
                ) {
                    return false;
                }

                return true;
            }
        );
        return array_shift($matchingProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveType(PointerInterface $pointer)
    {
        $property = $this->getProperty($pointer);
        if (!$property instanceof JMSPropertyMetadata) {
            return null;
        }
        return $property->type;
    }
}
