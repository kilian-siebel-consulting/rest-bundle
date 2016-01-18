# Transformer
The transformer is used to get information to a resource and to get the resource from the identifier.
 
## Service
The transformer can be loaded using the service id `ibrows_rest.resource_transformer`.

## Configuration
```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        resources:
            -   singular_name: resourceName
                plural_name: resourcesName
                class: resourceClass
```

Parameters:
 - `singular_name` - the name of the resource (for example "car")
 - `plural_name` - the plural name of the resource (for example "cars")
 - `class` - The class of the resource
 
## Methods
 - `getResourceProxy(string): object|null` - get a proxy object for a resource by the specified path
 - `getResourceName(ApiListableInterface): string|null` - get the singular name for the object
 - `getResourcesName(ApiListableInterface): string|null` - get the plural name for the object
 - `getResourcesPath(ApiListableInterface): string|null` - get the path for the object