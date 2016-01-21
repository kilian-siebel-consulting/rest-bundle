# Resource Deserialization Listener

## Purpose
The `ResourceDeserializationListener` is built to convert references to a resource in the request body to the resource itself.

## Requirements
The [transformer](../transformer.md) has to be configured correctly. Also all JMS types have to be configured correctly.
 
## Configuration
```yml
    ibrows_rest:
        listener:
            resource_deserialization:
                enabled: false
```
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`).

## Example

### Resource:

```php
    <?php
    namespace Acme\AcmeBundle\Resource;
    
    use JMS\Serializer\Annotation as JMS;
    
    class Resource
    {
        /**
         * @JMS\Type("Acme\AcmeBundle\Resource\User")
         * @JMS\Expose
         */
        private $owner
    }
```

### `POST` body:

```json
    {
        "car": "/users/27"
    }
```

### Result:
```
    Acme\AcmeBundle\Resource\Resource Object
    (
        [owner:Acme\AcmeBundle\Resource\Resource:private] => Acme\AcmeBundle\Resource\User Object
            (
                [id:Acme\AcmeBundle\Resource\User:private] => 27
            )
    
    )
```