# LinkParamConverter

**This Param Converter is a `ManipulateParamConverter`. [Read documentation](manipulate_param_converter.md).**

The LinkParamConverter provides an easy was to implement a [RFC](hhttps://tools.ietf.org/html/draft-snell-link-method-01) compliant LINK method.

It will use the parsed Links from the [LinkHeaderListener](../listener/link_header_listener.md) and apply them on an Entity.

## Usage
The same as any `ManipulateParamConverter`.

Additional Options:
- `relations: string[]` - The name of the supported Relations. **The method get{Relation} has to exist on the object!**

### Configuration
The LinkHeaderListener has to be enabled.

```yaml
    # app/config/config*.yml
    
    ibrows_rest:
        listener:
            link_header:
                enabled: true       
```

## Example

**Entity:**
```php
    <?php
    namespace Acme\AppBundle\Entity;
    
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\Common\Collections\Selectable;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * Car
     *
     * @ORM\Table(name="car")
     * @ORM\Entity
     */
    class Car
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;
    
        /**
         * @var Collection|Selectable|Category[]
         * @ORM\ManyToMany(targetEntity="Category", inversedBy="cars")
         * @ORM\JoinTable(name="cars_groups")
         */
        private $categories;
    
        /**
         * Car constructor.
         *
         * @param string $name
         */
        public function __construct($name)
        {
            $this
                ->setName($name)
                ->setCategories(new ArrayCollection())
            ;
        }
    
        /**
         * Get id
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }
    
        /**
         * @return Category[]|Collection|Selectable
         */
        public function getCategories()
        {
            return $this->categories;
        }
    
        /**
         * @param Category[]|Collection|Selectable $categories
         *
         * @return Car
         */
        public function setCategories(Collection $categories)
        {
            $this->categories = $categories;
            return $this;
        }
    }
```

**Controller Annotation:**
```php
    /**
     * @ParamConverter(
     *     "car",
     *     converter="link",
     *     class="AcmeAppBundle:Car",
     *     options = {
     *         "source" = "doctrine.orm",
     *         "relations" = { "categories" }
     *     },
     * )
     */
```

This example will:
- load the param using the param converter `doctrine.car`
- parse the Link in the request header
- fail with 400 if the link specifies an unknown relation
- look if the given resource is found using the `ibrows_rest.resource_transformer` and throw a 404 if not found
- look if the collection in `get{Relation}()` already contains the resource and throw a 409 if it does
- add the resource into the collection of `get{Relation}()`
- validate the object