# Link Header Listener

The Link Header Listener parses the request `Link` header and tries to look up the resource proxy via the [transformer](../transformer.md).

## Configuration
```yml
    ibrows_rest:
        listener:
            link_header:
                enabled: false
```
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`).