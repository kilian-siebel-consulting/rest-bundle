<?php
namespace Ibrows\RestBundle\HateoasConfigurationExtension;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Representation\RouteAwareRepresentation;

/**
 * Class PaginatedRepresentationConfigurationExtension
 * @package Ibrows\RestBundle\HateoasConfigurationExtension
 *
 * {@inheritDoc}
 */
class RepresentationConfigurationExtension implements ConfigurationExtensionInterface
{
    /**
     * @var string
     */
    private $exclusionGroup;

    /**
     * PaginatedRepresentationConfigurationExtension constructor.
     * @param string $exclusionGroup
     */
    public function __construct($exclusionGroup)
    {
        $this->exclusionGroup = $exclusionGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function decorate(ClassMetadataInterface $classMetadata)
    {
        //var_dump($classMetadata->getName());
        if($this->isCollection($classMetadata)) {
            $this->setRelations($classMetadata, array_map(
                [ $this, 'transformRelation' ],
                $classMetadata->getRelations()
            ));
        }
    }

    /**
     * @param ClassMetadataInterface $classMetadata
     * @return bool
     */
    private function isCollection(ClassMetadataInterface $classMetadata)
    {
        return $classMetadata->getName() === RouteAwareRepresentation::class ||
            is_subclass_of($classMetadata->getName(), RouteAwareRepresentation::class);
    }

    /**
     * @param ClassMetadataInterface $classMetadata
     * @param array                  $relations
     * TODO: Improve ClassMetadata and remove reflection
     */
    private function setRelations(ClassMetadataInterface $classMetadata, array $relations)
    {
        $classMetaDataReflection = new \ReflectionClass($classMetadata);
        $relationsProperty = $classMetaDataReflection->getProperty('relations');
        $relationsProperty->setAccessible(true);
        $relationsProperty->setValue($classMetadata, $relations);
    }

    /**
     * @param Exclusion|null $exclusion
     * @return Exclusion
     */
    private function transformExclusion(Exclusion $exclusion = null) {
        if(!$exclusion) {
            return new Exclusion(
                [
                    $this->exclusionGroup,
                ]
            );
        }

        $groups = is_array($exclusion->getGroups()) ? $exclusion->getGroups() : [];
        return new Exclusion(
            array_merge(
                $groups,
                [
                    $this->exclusionGroup,
                ]
            ),
            $exclusion->getSinceVersion(),
            $exclusion->getUntilVersion(),
            $exclusion->getMaxDepth(),
            $exclusion->getExcludeIf()
        );
    }

    /**
     * @param Relation $relation
     * @return Relation
     */
    private function transformRelation(Relation $relation)
    {
        return new Relation(
            $relation->getName(),
            $relation->getHref(),
            $relation->getEmbedded(),
            $relation->getAttributes(),
            $this->transformExclusion($relation->getExclusion())
        );
    }
}
