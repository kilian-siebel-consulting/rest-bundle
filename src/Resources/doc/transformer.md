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
                converter: converterServiceId
```

Parameters:
 - `singular_name` - the name of the resource (for example "car")
 - `plural_name` - the plural name of the resource (for example "cars")
 - `class` - The class of the resource
 - `converter` - The converter service id which should search for the resource
 
## Methods
 - `getResourceProxy(string): object|null` - get a proxy object for a resource by the specified path
 - `getResource(string): object|null` - get an object for a resource by the specified path
 - `getResourceConfig(ApiListableInterface): string|null` - get the config for the object
 - `getResourcePath(ApiListableInterface): string|null` - get the path for the object
 
## Converters
Converters must implement the `Ibrows\RestBundle\Transformer\Converter\ConverterInterface` interface and be registered as a service.

### Default Converters
#### Doctrine
There is a default converter for doctrine. The service id to use in the config is `ibrows_rest.resource_transformer.converter.doctrine`.