<?php
namespace Ibrows\RestBundle\Transformer;

use Doctrine\ORM\EntityManagerInterface;

class ResourceTransformer
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array<array<string, mixed>>
     */
    private $configuration;

    /**
     * ResourceTransformer constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param array                  $configuration
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        array $configuration
    ) {
        $this->entityManager = $entityManager;
        $this->configuration = $configuration;
    }

    /**
     * @param $path
     *
     * @return null|object
     */
    public function getResourceProxy($path)
    {
        list($resourceName, $id) = $this->parse($path);

        $resource = null;

        $resourceConfig = $this->getConfigByName($resourceName);
        if($resourceConfig) {
            $resource = $this->entityManager->getReference($resourceConfig['class'], $id);
        }

        return $resource;
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
            throw new \InvalidArgumentException('Path has to consist of exactly two parts.');
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
            return $resourceConfiguration['name'] === $resourceName;
        });
        return array_shift($matchingConfiguration);
    }
}