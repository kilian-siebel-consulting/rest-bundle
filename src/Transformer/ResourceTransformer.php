<?php
namespace Ibrows\RestBundle\Transformer;

use FOS\RestBundle\Util\Inflector\InflectorInterface;
use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Transformer\Converter\ConverterInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class ResourceTransformer implements TransformerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ConverterInterface[]
     */
    private $converters;
    
    /** 
     * @var InflectorInterface 
     */
    private $inflector;

    /**
     * @var array
     */
    private $urlPrefixes;

    /**
     * ResourceTransformer constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router,
        InflectorInterface $inflector,
        array $urlPrefixes = []
    ) {
        $this->router = $router;
        $this->inflector = $inflector;
        $this->converters = [];
        $this->urlPrefixes = $urlPrefixes;
    }

    /**
     * @param string $path
     * @return array|null
     */
    private function findResourceInformation($path)
    {
        $rawPath = $this->extractResourcePathFromUrl($path);

        if (null !== ($resourceInfo = $this->findResourceInformationFromRouter($rawPath))) {
            return $resourceInfo;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceProxy($path)
    {
        $resourceInfo = $this->findResourceInformation($path);

        if (null !== $resourceInfo) {
            list($entityClass, $id, $converter) = $resourceInfo;

            return $this->converters[$converter]->getResourceProxy(
                $entityClass,
                $id
            );
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($path)
    {
        $resourceInfo = $this->findResourceInformation($path);

        if (null !== $resourceInfo) {
            list($entityClass, $id, $converter) = $resourceInfo;

            return $this->converters[$converter]->getResource(
                $entityClass,
                $id
            );
        }

        return null;
    }

    private function convertRouteToConfig(Route $route)
    {
        $className = $route->getOption(self::RESOURCE_ENTITY_CLASS_OPTION);
        
        $converter = self::RESOURCE_DEFAULT_CONVERTER;
        if ($route->hasOption(self::RESOURCE_CONVERTER_OPTION)) {
            $converter = $route->getOption(self::RESOURCE_CONVERTER_OPTION);
        }
        
        if ($route->hasOption(self::RESOURCE_SINGULAR_NAME)) {
            $singular = $route->getOption(self::RESOURCE_SINGULAR_NAME);
        } else {
            $classNameParts = explode('\\', $className);
            end($classNameParts);
            $singular = strtolower(current($classNameParts));
        }

        if ($route->hasOption(self::RESOURCE_PLURAL_NAME)) {
            $plural = $route->getOption(self::RESOURCE_PLURAL_NAME);
        } else {
            $plural = $this->inflector->pluralize($singular);
        }
        
        return [
            'route' => $route->getDefault('_route'),
            'class' => $className,
            'converter' => $converter,
            'singular_name' => $singular,
            'plural_name' => $plural
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getResourceConfig(ApiListableInterface $object)
    {
        if (null !== ($route = $this->getResourceRoute($object))) {
            return $this->convertRouteToConfig($route);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcePath(ApiListableInterface $object)
    {
        if (null !== ($route = $this->getResourceRoute($object))) {
            $id = $object->getId();
            $idname = $route->getOption(self::RESOURCE_ENTITY_ID_OPTION);
            
            return $this->router->generate($route->getDefault('_route'), [
                $idname => $id
            ]);
        }
        return null;
    }

    /**
     * @param string $className
     * @param Route $route
     * @return bool
     */
    private function isRouteResponsibleForEntity($className, Route $route)
    {
        $routeClassName = $route->getOption(self::RESOURCE_ENTITY_CLASS_OPTION);
        return $className === $routeClassName || is_subclass_of($className, $routeClassName);        
    }

    /**
     * @param ApiListableInterface $object
     * @return null|Route
     */
    private function getResourceRoute(ApiListableInterface $object)
    {
        return $this->getResourceRouteByClassName(get_class($object));
    }

    /**
     * @param string $className
     * @return null|Route
     */
    private function getResourceRouteByClassName($className)
    {
        foreach($this->router->getRouteCollection() as $route) {
            if ($route->hasOption(self::RESOURCE_ENTITY_CLASS_OPTION) &&
                $this->isRouteResponsibleForEntity($className, $route)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @param string $url
     * @return string
     */
    private function extractResourcePathFromUrl($url)
    {
        $components = parse_url($url);

        if (false === $components) {
            throw new InvalidArgumentException('The given path "' . $url . '" does not look like an URL');
        }

        if (!isset($components['path'])) {
            throw new InvalidArgumentException('URL has no path component');
        }

        $rawPath = $components['path'];

        $isValidUrl = false;
        foreach($this->urlPrefixes as $pathInfoPrefix) {
            if (strpos($rawPath, $pathInfoPrefix) === 0) {
                $rawPath = substr($rawPath, strlen($pathInfoPrefix));
                $isValidUrl = true;
            }
        }
        
        if (count($this->urlPrefixes) > 0 && !$isValidUrl) {
            throw new InvalidArgumentException('The given path "' . $url . '" does not have a required prefix (one of "'
                . implode('", "', $this->urlPrefixes) . '")');
        }
        
        return $rawPath;
    }

    
    const RESOURCE_ENTITY_CLASS_OPTION = 'resourceEntity';
    const RESOURCE_ENTITY_ID_OPTION = 'resourceIdAttribute';
    const RESOURCE_CONVERTER_OPTION = 'resourceConverter';
    const RESOURCE_SINGULAR_NAME = 'resourceSingularName';
    const RESOURCE_PLURAL_NAME = 'resourcePluralName';
    const RESOURCE_DEFAULT_CONVERTER = 'ibrows_rest.resource_transformer.converter.doctrine';

    /**
     * @return string
     */
    private function getRequestMethod()
    {
        if ($this->router->getContext()) {
            $requestMethod = $this->router->getContext()->getMethod();
        } else {
            $requestMethod = Request::METHOD_GET;
        }
        
        return $requestMethod;
    }

    /**
     * @param string $method
     */
    private function setRequestMethod($method)
    {
        if ($this->router->getContext()) {
            $this->router->getContext()->setMethod($method);
        }
    }
    
    /**
     * @param $path
     * @return array|null
     */
    private function findResourceInformationFromRouter($path)
    {
        // store and reset the request method
        $requestMethod = $this->getRequestMethod();
        $this->setRequestMethod(Request::METHOD_GET);
        
        try {
            $parameters = $this->router->match($path);
            $this->setRequestMethod($requestMethod);
        } catch(ResourceNotFoundException $e) {
            $this->setRequestMethod($requestMethod);
            return null;
        } catch(MethodNotAllowedException $e) {
            $this->setRequestMethod($requestMethod);
            return null;
        }
        

        if (!isset($parameters['_route'])) {
            return null;
        }

        $route = $this->router->getRouteCollection()->get($parameters['_route']);

        if (null === $route) {
            return null;
        }

        if (!$route->hasOption(self::RESOURCE_ENTITY_CLASS_OPTION) ||
            !$route->hasOption(self::RESOURCE_ENTITY_ID_OPTION)) {
            return null;
        }

        if (!isset($parameters[$route->getOption(self::RESOURCE_ENTITY_ID_OPTION)])) {
            return null;
        }

        $converter = self::RESOURCE_DEFAULT_CONVERTER;
        if ($route->hasOption(self::RESOURCE_CONVERTER_OPTION)) {
            $converter = $route->getOption(self::RESOURCE_CONVERTER_OPTION);
        }
        
        return [
            $route->getOption(self::RESOURCE_ENTITY_CLASS_OPTION),
            $parameters[$route->getOption(self::RESOURCE_ENTITY_ID_OPTION)],
            $converter,
        ];
    }
    
    /**
     * @param string             $name
     * @param ConverterInterface $converter
     */
    public function addConverter($name, ConverterInterface $converter)
    {
        $this->converters[$name] = $converter;
    }

    /**
     * @param string $class
     * @return boolean
     */
    public function isResource($class)
    {
        return null !== $this->getResourceRouteByClassName($class);
    }

    /**
     * @param mixed $path
     * @return boolean
     */
    public function isResourcePath($path)
    {
        if (!is_string($path)) {
            return false;
        }

        try {
            $this->extractResourcePathFromUrl($path);
        } catch(InvalidArgumentException $e) {
            return false;
        }
        
        return true;
    }
}
