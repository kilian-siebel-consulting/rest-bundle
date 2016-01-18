# Patching System

To see how the patching system works with the Annotation, please read [this article](param_converter/patch_param_converter.md).

The patching system is built to execute [RFC](https://tools.ietf.org/html/rfc6902) compliant patches on an object.

## Service
The service of the Patch Execution class is `ibrows_rest.patch.executioner`.

## Usage
```php
    <?php
    use Ibrows\RestBundle\Patch\OperationInterface;

    $executioner = $container->get('ibrows_rest.patch.executioner');
    $operations = [
        new SubClassOfOperationInterface(),
    ];
        
    $executioner->execute($object, $executioner);
```

## JMS Serializer
To get an Operation Collection, simply decode the PATCH request body using JMS.

To add new PatchTypes, the discriminator of `Ibrows\RestBundle\Patch\Operation` has to be overridden.

## Ibrows\RestBundle\Patch\OperationInterface explained

The OperationInterface only requires you to provide two functions:
- `getPath(): string` - Provide the path of the Operation. This is automatically provided if you use the abstract class `Ibrows\RestBundle\Patch\Operation`.
- `apply(object, JMS\Serializer\Metadata\PropertyMetadata\PropertyMetadata): void` - Apply the operation on the given object.
