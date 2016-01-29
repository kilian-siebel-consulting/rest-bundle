# Exception Controller

The exception controller overrides the default twig exception controller and the default fos exception controller.
By default it only overrides the controller if it is not set to ensure compatibility with FOSREstBundle when twig is
not enabled (which is useful for api-only projects which doesn't have any need for views).
 
## Configuration
```yml
    ibrows_rest:
        exception_controller:
            enabled: true
            force_default: false
```
- The `enabled` boolean setting allows you to enable or disable the controller (default: `false`). 
- If `force_default` is `true` it always overrides the twig exception controller (default: `false`). The
  default behaviour is to just override the controller if it is not yet set. If you are not using twig
  you probably want to set this to `true`.

## Information

After enabling the extension controller the `errors` value is defined with the exception information provided
by exception which implements the `DisplayableException` interface.

## Example

First we declare a new exception class which implements `DisplayableException`. The extended error information
is prepared in the `toArray` method required by the interface:

```php
class MyException extends Exception implements DisplayableException
{
    private $errorInformation;
    
    public function setErrorInformation(array $errorInformation)
    {
        $this->errorInformation = $errorInformation;
    }
    
    
    public funciton toArray() {
        return [
            'myErrorData' => $this->errorInformation
        ];
    }
}
```

Now, wen a `MyException` is thrown the `errors` element of the exception response would contain the result of the
`toArray` call.

If we would throw the following exception:
```php
$error = new MyException("Something is wrong", 400);
$error->setErrorInformation([
    'property' => 'myproperty',
    'message' => 'this property does not exist',
]);
```

The response from calling it would look like:

```json
{
    "code":400,
    "message":"Something is wrong",
    "errors": {
        "myErrorData": {
            "property": "myproperty",
            "message": "This value should not be null.",
        }
    }
```
