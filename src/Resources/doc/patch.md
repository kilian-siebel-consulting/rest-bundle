# Patch

The patching system is able to apply JSON Patches to arrays & objects. 

# Custom Address

To support custom types, implement `\Ibrows\RestBundle\Patch\AddressResolverInterface` and tag your service with the tag `ibrows_rest.patch.address_resolver`.
The address resolver has to return a `\Ibrows\RestBundle\Patch\AddressInterface`.

# Custom Operation

To add a custom operation, implement `\Ibrows\RestBundle\Patch\OperationApplierInterface` and tag your service with the tag `ibrows_rest.patch.operation_applier`.

Tag Attributes:
 - `operation` - The name of the operation to support.
 - `priority` - The priority of your implementation.
