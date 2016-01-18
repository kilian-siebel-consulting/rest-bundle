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
     * @param array                  $configuration
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
        if($resourceConfig) {
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
        if($resourceConfig) {
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
        if($this->getConfigByClass($object)) {
            return $this->getConfigByClass($object);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcePath(ApiListableInterface $object)
    {
        if($this->getConfigByClass($object)) {
            return '/' . $this->getConfigByClass($object)['plural_name'] . '/' . $object->getId();
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
        if(count($parts) !== 2) {
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
        $matchingConfiguration = array_filter($this->configuration, function($resourceConfiguration) use ($resourceName) {
            return $resourceConfiguration['plural_name'] === $resourceName;
        });
        return array_shift($matchingConfiguration);
    }

    /**
     * @param ApiListableInterface $object
     *
     * @return array<string, mixed>
     */
    private function getConfigByClass(ApiListableInterface $object)
    {
        $matchingConfiguration = array_filter($this->configuration, function($resourceConfiguration) use ($object) {
            return get_class($object) === $resourceConfiguration['class'] ||
            is_subclass_of($object, $resourceConfiguration['class']);
        });
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
}