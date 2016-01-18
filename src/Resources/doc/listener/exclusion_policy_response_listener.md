# Exclusion Policy Response Listener

The exclusion policy listener allows the user of the api to supply a predefined serializer group name via
query-parameter which is then used by JMSSerializer to serialize the response. That way you can limit
the result size for certain queries.

## Configuration

The default configuration would look like this: 

```yml
    ibrows_rest:
        listener:
            exclusion_policy:
                enabled: true
                param_name: expolicy
```                
                
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`). 
 - The `param_name` describes which query-parameter is used to get the serializer group name (default: `expolicy`). 
   Any name is acceptable as long as it doesn't colide with other parameter names.
 
## Usage
 
If you want to use the listener in your action you have to first add the `@QueryParam` annotation:
 
```php
    /**
      * ...
      * @FOSRest\QueryParam(name="expolicy", requirements="(car_list|car_detail)", default="car_list", strict=true, description="Serialization group")
      * ...
      */
```
      
The name of the query parameter is of course given by the `param_name` configuration setting. 
      
In this example we have two serialization groups we want to use: `car_list` and `car_detail`. You *should* limit the 
list of possible values in the `requirements` of the `QueryParam` annotation. You *should* also supply a default value
in case the action is called without the parameter.