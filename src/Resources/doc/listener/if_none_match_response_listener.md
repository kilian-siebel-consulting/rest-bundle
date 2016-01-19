# If-None-Match Response Listener

The If-None-Match Response Listener checks if there is a If-None-Match request header and the response has an ETag set. If those two match the response will be changed to a `304 Not Modified`.

## Configuration
```yml
    ibrows_rest:
        listener:
            if_none_match:
                enabled: false
```
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`).