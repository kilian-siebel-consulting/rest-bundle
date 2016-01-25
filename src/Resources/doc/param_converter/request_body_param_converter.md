# RequestBodyParamConverter

Fail with `400 Bad Request` if a validation error occurred in the `fos_rest.request_body`.

### Configuration
```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        param_converter:
            common:
                fail_on_validation_error: true
                validation_errors_argument: null
            request_body:
                enabled: true
                fail_on_validation_error: true
                validation_errors_argument: null
```

 - `enabled` - Enable / Disable the param converter (default: `true`)
 - `fail_on_validation_error` - Fail if a validation Error is detected (default: `true`)
 - `validation_errors_argument` - Store the validation errors in which key in the request attributes. **has to be the same as the one from FOSRest**
 
Values from `common` can be overwritten in `request_body`.ct