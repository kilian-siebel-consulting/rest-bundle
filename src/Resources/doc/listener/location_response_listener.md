# Location response listener

This listener allows you to define a location header using an annotation. 

## Configuration
```yml
    ibrows_rest:
        listener:
            location:
                enabled: false
```
 - The `enabled` boolean setting allows you to enable or disable the listener globally (default: `false`).

## Usage

The value of the Location header can be specified via annotation. You can define a route parameter which
uses the normal symfony routing service.

```php
    /*
     * ...
     * @IbrowsAPI\View(
     *     statusCode=201,
     *     location=@IbrowsAPI\Route(
     *          route="ibrows_example_car_get",
     *          params={
     *              "car"="expr(car.getId())"
     *          }
     *     )
     * )
     */
     public function postAction(Car $car)
     ...
```

In this example you see how the route is defined. The route has a parameter "car" which can also defined. Since the
id of a car is dynamic, an expression is used to fetch it dynamically - the listener takes care of 
evaluating the expression.

In this case the status code is 201, but the listener does not check the status code. So you have to take care
of using an approriate status code.