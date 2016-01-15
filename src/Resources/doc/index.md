# IbrowsRestBundle

The IbrowsRestBundle is an addition to the FOSRestBundle. It provides many small improvements.

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
    $ composer require ibrows/rest-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
    <?php
    // app/AppKernel.php
    
    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new Ibrows\RestBundle\IbrowsRestBundle(),
            );
            
            // ...
        }
    }
```

## Listeners
 - [Debug Listener](listener/debug_response_listener.md)
 - [Exclusion Policy Listener](listener/exclusion_policy_response_listener.md)
 - [Location Response Listener](listener/location_response_listener.md)

## Reference Configuration

```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        resources:
            -   name: resourceName
                class: resourceClass
        listener:
            debug:
                enabled: false
                key_name: _debug
            exclusion_policy:
                enabled: false
                param_name: expolicy
               
```


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

```bash
    $ composer install --dev
```

Run it using PHPUnit:

```bash
    $ phpunit
```