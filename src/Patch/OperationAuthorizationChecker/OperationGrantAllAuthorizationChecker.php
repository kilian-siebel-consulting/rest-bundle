<?php
namespace Ibrows\RestBundle\Patch\OperationAuthorizationChecker;

use Ibrows\RestBundle\Patch\OperationAuthorizationCheckerInterface;
use Ibrows\RestBundle\Patch\OperationInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OperationGrantAllAuthorizationChecker implements OperationAuthorizationCheckerInterface
{


    /**
     * @param array $operations
     * @param       $object
     * @return bool
     */
    public function isGranted(array $operations, $object)
    {
        return true;
    }
}
