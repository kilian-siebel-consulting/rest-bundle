# PatchParamConverter

**This Param Converter is a `ManipulateParamConverter`. [Read documentation](manipulate_param_converter.md).**

The PatchParamConverter provides an easy was to implement a [RFC](https://tools.ietf.org/html/rfc6902) compliant PATCH method.

### Configuration
```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        param_converter:
            common:
                fail_on_validation_error: true
                validation_errors_argument: null
            patch:
                enabled: true
                fail_on_validation_error: true
                validation_errors_argument: null
```

 - `enabled` - Enable / Disable the param converter (default: `true`)
 - `fail_on_validation_error` - Fail if a validation Error is detected (default: `true`)
 - `validation_errors_argument` - Store the validation errors in which key in the request attributes
 
Values from `common` can be overwritten in `patch`.

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
