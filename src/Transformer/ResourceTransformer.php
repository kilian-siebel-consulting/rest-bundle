<?php
namespace Ibrows\RestBundle\Transformer;

use Ibrows\RestBundle\Model\ApiListableInterface;
use Ibrows\RestBundle\Transformer\Converter\ConverterInterface;
use InvalidArgumentException;

class ResourceTransformer implements TransformerInterface
{
    /**
     * @var array<array<string, mixed>>
     */
    private $configuration;

    /**
     * @var ConverterInterface[]
     */
    private $converters;

    /**
     * ResourceTransformer constructor.
     *
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->configuration = $configuration;
        $this->converters = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceProxy($path)
    {
        list($resourceName, $id) = $this->parse($path);

        $resourceConfig = $this->getConfigByName($resourceName);
        if ($resourceConfig) {
            return $this->converters[$resourceConfig['converter']]->getResourceProxy(
                $resourceConfig['class'],
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
        list($resourceName, $id) = $this->parse($path);

        $resourceConfig = $this->getConfigByName($resourceName);
        if ($resourceConfig) {
            return $this->converters[$resourceConfig['converter']]->getResource(
                $resourceConfig['class'],
                $id
            );
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceConfig(ApiListableInterface $object)
    {
        $className = get_class($object);

        if ($this->getConfigByClass($className)) {
            return $this->getConfigByClass($className);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcePath(ApiListableInterface $object)
    {
        $className = get_class($object);

        if ($this->getConfigByClass($className)) {
            return '/' . $this->getConfigByClass($className)['plural_name'] . '/' . $object->getId();
        }
        return null;
    }

    /**
     * @param string $path
     * @return array
     */
    private function parse($path)
    {
        $parts = explode('/', $path);
        $parts = array_filter($parts);
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Path has to consist of exactly two parts.');
        }
        return array_values($parts);
    }

    /**
     * @param $resourceName
     *
     * @return array<string, mixed>
     */
    private function getConfigByName($resourceName)
    {
        $matchingConfiguration = array_filter(
            $this->configuration,
            function ($resourceConfiguration) use ($resourceName) {
                return $resourceConfiguration['plural_name'] === $resourceName;
            }
        );
        return array_shift($matchingConfiguration);
    }

    /**
     * @param string $className
     *
     * @return array<string, mixed>
     */
    private function getConfigByClass($className)
    {
        $matchingConfiguration = array_filter(
            $this->configuration,
            function ($resourceConfiguration) use ($className) {
                return $className === $resourceConfiguration['class'] ||
                is_subclass_of($className, $resourceConfiguration['class']);
            }
        );
        return array_shift($matchingConfiguration);
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
        return $this->getConfigByClass($class) !== null;
    }

    /**
     * @param mixed $data
     * @return boolean
     */
    public function isResourcePath($path)
    {
        if (!is_string($path)) {
            return false;
        }

        $matches = preg_match('(^\/.*\/\d*)', $path);
        if ($matches === 0) {
            return false;
        }

        return true;
    }
}
