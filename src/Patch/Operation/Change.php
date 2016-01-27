<?php
namespace Ibrows\RestBundle\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Class Change
 *
 * @package Ibrows\RestBundle\Patch\Operation
 * @ExclusionPolicy("all")
 */
class Change extends ValueOperation
{
    /**
     * {@inheritdoc}
     */
    public function apply($object, PropertyMetadata $property)
    {
        $property->setValue($object, $this->getValue());
    }
}
