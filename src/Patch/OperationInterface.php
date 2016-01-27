<?php
namespace Ibrows\RestBundle\Patch;

use JMS\Serializer\Metadata\PropertyMetadata;

interface OperationInterface
{
    /**
     * Apply the Operation to the given Property of given object
     *
     * @param object           $object
     * @param PropertyMetadata $property
     */
    public function apply($object, PropertyMetadata $property);

    /**
     * @return string
     */
    public function getPath();
}
