<?php
namespace Ibrows\RestBundle\Transformer\Converter;

use Ibrows\RestBundle\Model\ApiListableInterface;

interface ConverterInterface
{
    /**
     * This method should return a proxy to the desired object.
     * If there is no proxy mechanism, the converter should return
     * the same as getResource.
     *
     * @param string $className
     * @param mixed  $identifier a scalar identifier
     * @return ApiListableInterface
     */
    public function getResourceProxy($className, $identifier);

    /**
     * @param string $className
     * @param mixed  $identifier a scalar identifier
     * @return ApiListableInterface
     */
    public function getResource($className, $identifier);
}