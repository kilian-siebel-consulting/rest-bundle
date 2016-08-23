<?php
namespace Ibrows\RestBundle\Patch;


interface OperationAuthorizationCheckerInterface
{

    /**
     * @param OperationInterface[] $operations
     * @param                    $object
     * @return bool
     */
    public function isGranted(array $operations, $object);
}
