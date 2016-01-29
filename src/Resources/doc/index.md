# IbrowsRestBundle

The IbrowsRestBundle is an addition to the FOSRestBundle. It provides many small improvements.

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
    $ composer require ibrows/rest-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
    <?php
    // app/AppKernel.php
    
    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Ibrows\RestBundle\IbrowsRestBundle(),
            );
            
            // ...
        }
    }
```

## Listeners
- Debug Listener - [Read Documentation](listener/debug_response_listener.md)
- Exclusion Policy Listener - [Read Documentation](listener/exclusion_policy_response_listener.md)
- Location Response Listener - [Read Documentation](listener/location_response_listener.md)
- Collection Decoration Listener - [Read Documentation](listener/collection_decoration_listener.md)
- ETag Response Listener - [Read Documentation](listener/etag_response_listener.md)
- If-None-Match Response Listener - [Read Documentation](listener/if_none_match_response_listener.md)
- Link Header Listener - [Read Documentation](listener/link_header_listener.md)
- Resource Deserialization Listener - [Read Documentation](listener/resource_deserialization_listener.md)

## Param Converters
- `abstract ManipulateParamConverter` - [Read Documentation](param_converter/manipulate_param_converter.md)
- `LinkParamConverter` - [Read Documentation](param_converter/link_param_converter.md)
- `PatchParamConverter` - [Read Documentation](param_converter/patch_param_converter.md)
- `RequestBodyParamConverter` - [Read Documentation](param_converter/request_body_param_converter.md)
- `UnlinkParamConverter` - [Read Documentation](param_converter/unlink_param_converter.md)
 
## Controllers
- `ExceptionController`  - [Read Documentation](controller/exception_controller.md)
 
## Patch

The patching system is used to apply patches to an object in the `PatchParamConverter`. It can also be used standalone.

[Read Documentation](patch.md)
 
## Transformer
[Read Documentation](transformer.md)

## Reference Configuration

```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        resources:
            -   singular_name: resourceName
                plural_name: resourcesName
                class: resourceClass
                converter: converterServiceId
        param_converter:
            common:
                fail_on_validation_error: true
                validation_errors_argument: null
            link:
                enabled: true
                fail_on_validation_error: true
                validation_errors_argument: null
            patch:
                enabled: true
                fail_on_validation_error: true
                validation_errors_argument: null
            request_body:
                enabled: true
                fail_on_validation_error: true
                validation_errors_argument: null
            unlink:
                enabled: true
                fail_on_validation_error: true
                validation_errors_argument: null
        listener:
            cache:
                enabled: false
            collection_decorator:
                enabled: false
            debug:
                enabled: false
                key_name: _debug
            etag:
                enabled: false
                hashing_algorithm: crc32
            exclusion_policy:
                enabled: false
                param_name: expolicy
            if_none_match:
                enabled: false
            link_header:
                enabled: false
            location:
                enabled: false  
            resource_deserialization:
                enabled: false  
        exception_controller:
            enabled: true
            force_default: false
            controller: 'ibrows_rest.controller.exception:showAction'                
```


## Testing

Setup the test suite using [Composer](http://getcomposer.org/):

```bash
    $ composer install --dev
```

Run it using PHPUnit:

```bash
    $ phpunit
```
