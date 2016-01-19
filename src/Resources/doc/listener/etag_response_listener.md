# ETag Response Listener

The ETag Response Listener has only one really simple goal. It sets the ETag header on every GET / HEAD response.

## Configuration
```yml
    ibrows_rest:
        listener:
            etag:
                enabled: false
                hashing_algorithm: crc32
```
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`). 
 - The `hashing_algorithm` chooses which hashing algorithm to use (default: `crc32`). 
   Any hashing function which is present in `hash_algos()` is valid.