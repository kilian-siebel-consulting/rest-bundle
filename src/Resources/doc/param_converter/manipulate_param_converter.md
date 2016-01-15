# ManipulateParamConverter

`ManipulateParamConverter` is a base for any param converter which does not source the param it uses.
It rather loads the param from the source and does some modifications on it.

The actual source of the data, has to be specified using the option `source`.

**Please do not use this class to wrap a `ManipulateParamConverter` into itself.** The result will get very messy.

## Methods

The class provides two helper methods:

- `getObject(Symfony\Component\HttpFoundation\Request, Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter)` - Get the source object.
- `validate(object, Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter, Symfony\Component\HttpFoundation\Request)` - Validate the object