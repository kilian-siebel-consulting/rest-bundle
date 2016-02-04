<?php
namespace Ibrows\RestBundle\Patch\Operation;

use Ibrows\RestBundle\Patch\Operation;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Metadata\PropertyMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class Change
 *
 * @package Ibrows\RestBundle\Patch\Operation
 * @ExclusionPolicy("all")
 */
class Replace extends ValueOperation
{
    /**
     * {@inheritdoc}
     */
    public function apply($object, PropertyMetadata $property)
    {
        $typeName = isset($property->type['name']) ? $property->type['name'] : null;
        
        if (null !== $typeName
            && is_object($this->getValue())
            && (class_exists($typeName) || interface_exists($typeName))
            && !$this->getValue() instanceof $typeName) {
            
            throw new BadRequestHttpException("Invalid change value for " . $property->name);
        }
        
        $property->setValue($object, $this->getValue());
    }
}
