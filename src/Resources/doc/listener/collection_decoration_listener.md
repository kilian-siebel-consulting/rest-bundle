# Collection Decoration Listener

The Collection Decoration Listener wraps Hateoas list representations around list responses.

The listener first wraps a `Ibrows\RestBundle\Representation\CollectionRepresentation` around the result set and then loops all available decorators. 
 
## Configuration
```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        listener:
            collection_decorator:
                enabled: false
               
```
 - `enabled` - Enable / Disable the listener (default: `false`)
 
## Decorators
Decorators can be added by specifying the tag `ibrows_rest.collection_decorator` and implementing the Interface `Ibrows\RestBundle\CollectionDecorator\DecoratorInterface`.

### Default Decorators
 - `Ibrows\RestBundle\CollectionDecorator\LastIdDecorator` - Wrap a `Ibrows\RestBundle\Representation\LastIdRepresentation` around the result set.
 - `Ibrows\RestBundle\CollectionDecorator\OffsetDecorator` - Wrap a `Ibrows\RestBundle\Representation\OffsetRepresentation` around the result set.
 - `Ibrows\RestBundle\CollectionDecorator\PaginatedDecorator` - Wrap a `Ibrows\RestBundle\Representation\PaginationRepresentation` around the result set.