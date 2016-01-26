# Exception Controller

The exception controller overrides the default twig exception controller. By default it only overrides the 
controller if it is not set to ensure compatibility with FOSREstBundle when twig is not enabled (which is
 useful for api-only projects which doesn't have any need for views).
 
This component basically works by setting the parameter `twig.exception_listener.controller` parameter. 

## Configuration
```yml
    ibrows_rest:
        exception_controller:
            enabled: true
            force_default: false
            controller: 'ibrows_rest.controller.exception:showAction'
```
- The `enabled` boolean setting allows you to enable or disable the controller (default: `false`). 
- If `force_default` is `true` it always overrides the twig exception controller (default: `false`). The
  default behaviour is to just override the controller if it is not yet set. If you are not using twig
  you probably want to set this to `true`.
- The `controller` scalar setting allows you to change the action which is executed in case of an exception. 
