<?php
namespace Ibrows\RestBundle\Patch\OperationAuthorizationChecker;

use Ibrows\RestBundle\Patch\OperationAuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class OperationSecurityAuthorizationChecker implements OperationAuthorizationCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * OperationSecurityAuthorizationChecker constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }


    /**
     * @param AuthorizationCheckerInterface[] $operations
     * @param                                 $object
     * @return bool
     */
    public function isGranted(array $operations, $object)
    {
        return $this->authorizationChecker->isGranted($operations, $object);
    }
}
