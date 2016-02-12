#Cache Header Listener
This listener adds headers to the `Vary` header.

## Configuration

```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        listener:
            vary_header:
                enabled: false
                headers: []
```

 - `enabled` - Enable / Disable the listener (default: `false`)
 - `headers` - Headers to add to Vary Header (default: `[]`, type: `string`)
