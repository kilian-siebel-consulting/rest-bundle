# Debug Response Listener

The debug listener adds some information to an API response, which would normally be displayed in the web profiler toolbar.

## Requirements

The debug listener only works for `application/json` responses. If the Content Type specifies any other format, it won't work.

## Configuration

The default configuration would look like this: 

```yml
    ibrows_rest:
        listener:
            debug:
                enabled: false
```
                
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`).
 
## Data in Debug Object

### Profiler Data

By default those fields are added automatically:
- `tokenUrl`
- `ip`
- `method`
- `url`
- `time`
- `statusCode`

### Collector Data Converters

#### Default
- `Db` - [ `mapping_errors`, `invalid_entities`, `query_count`, `query_time` ]
- `Memory` - `memory`
- `Time` - `time_elapsed`
- `Security` - [ `enabled`, `user`, `roles`, `authenticated`, `token` ]


#### Custom

All services tagged with `ibrows_rest.listener.view_debug.converter` are asked if they support any `DataCollectorInterface`.
If they do, the Converter is asked to convert the `DataCollectorInterface` to a scalar type which will be merged into the `_debug` object.

```xml
    <!-- services.xml -->
    <service id="app_bundle.listener.view_debug.converter.security" class="AppBundle\DebugConverter\WhateverConverter">
        <tag name="ibrows_rest.listener.view_debug.converter" />
    </service>
```

```php
    <?php
    namespace AppBundle\DebugConverter;
    
    use Ibrows\RestBundle\Debug\Converter\ConverterInterface;
    use Some\Bundle\AnyCollector;
    use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
    
    class WhateverConverter implements ConverterInterface
    {
        /**
         * {@inheritdoc}
         * @param AnyCollector $dataCollector
         */
        public function convert(DataCollectorInterface $dataCollector)
        {
            return $dataCollector->getAnythingScalar();
        }
    
        /**
         * {@inheritdoc}
         */
        public function supports(DataCollectorInterface $dataCollector)
        {
            return $dataCollector instanceof AnyCollector;
        }
    
        /**
         * {@inheritdoc}
         */
        public function getName()
        {
            return 'whatever';
        }
    }
```
