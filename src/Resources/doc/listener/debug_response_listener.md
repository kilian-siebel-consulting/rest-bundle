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