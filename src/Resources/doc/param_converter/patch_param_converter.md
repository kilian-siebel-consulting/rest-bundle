# PatchParamConverter

**This Param Converter is a `ManipulateParamConverter`. [Read documentation](manipulate_param_converter.md).**

The PatchParamConverter provides an easy was to implement a [RFC](https://tools.ietf.org/html/rfc6902) compliant PATCH method.

## Usage
The same as any `ManipulateParamConverter`. There are no additional options.

## Example
```php
    <?php
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    
    /**
     * @ParamConverter(
     *     "exampleEntity",
     *     converter="patch",
     *     class="IbrowsAppBundle:ExampleEntity",
     *     options={
     *         "source" = "doctrine.orm",
     *     }
     * )
     */
```

This example will:
- load the contents of the param converter `doctrine.orm`
- parse the request body
- validate the resulting operations
- apply the operations on the contents of the param converter `doctrine.orm` using the [service](../patch.md)
- validate the applied object