<?php
namespace Ibrows\RestBundle\Representation;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\OffsetRepresentation as BaseOffsetRepresentation;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Class OffsetRepresentation
 *
 * @package Ibrows\RestBundle\Representation
 *
 * @Hateoas\RelationProvider("getRelations")
 */
class OffsetRepresentation extends BaseOffsetRepresentation
{
    /**
     * @var Exclusion
     */
    private $exclusion;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        $inline,
        $route,
        array $parameters = [],
        $offset,
        $limit,
        $total = null,
        $offsetParameterName = null,
        $limitParameterName = null,
        $absolute = false,
        Exclusion $exclusion = null
    ) {
        if ($exclusion === null) {
            $exclusion = new Exclusion(
                [
                    'hateoas_list'
                ]
            );
        }

        parent::__construct(
            $inline,
            $route,
            $parameters,
            $offset,
            $limit,
            $total,
            $offsetParameterName,
            $limitParameterName,
            $absolute
        );

        $this->exclusion = $exclusion;
    }

    /**
     * @param OffsetRepresentation   $object
     * @param ClassMetadataInterface $classMetadata
     * @return Relation[]
     * @codeCoverageIgnore
     */
    public function getRelations($object, ClassMetadataInterface $classMetadata)
    {
        return [
            new Relation(
                'first',
                new Route(
                    'expr(object.getRoute())',
                    'expr(object.getParameters(0))',
                    'expr(object.isAbsolute())'
                ),
                null,
                [],
                $this->exclusion
            ),
            new Relation(
                'last',
                new Route(
                    'expr(object.getRoute())',
                    'expr(object.getParameters((object.getTotal() - 1) - (object.getTotal() - 1) % object.getLimit()))',
                    'expr(object.isAbsolute())'
                ),
                null,
                [],
                new Exclusion(
                    $this->exclusion->getGroups(),
                    $this->exclusion->getSinceVersion(),
                    $this->exclusion->getUntilVersion(),
                    $this->exclusion->getMaxDepth(),
                    'expr(object.getTotal() === null)'
                )
            ),
            new Relation(
                'next',
                new Route(
                    'expr(object.getRoute())',
                    'expr(object.getParameters(object.getOffset() + object.getLimit()))',
                    'expr(object.isAbsolute())'
                ),
                null,
                [],
                new Exclusion(
                    $this->exclusion->getGroups(),
                    $this->exclusion->getSinceVersion(),
                    $this->exclusion->getUntilVersion(),
                    $this->exclusion->getMaxDepth(),
                    'expr(object.getTotal() !== null && (object.getOffset() + object.getLimit()) >= object.getTotal())'
                )
            ),
            new Relation(
                'previous',
                new Route(
                    'expr(object.getRoute())',
                    'expr(object.getParameters((object.getOffset() > object.getLimit()) ? object.getOffset() - object.getLimit() : 0))',
                    'expr(object.isAbsolute())'
                ),
                null,
                [],
                new Exclusion(
                    $this->exclusion->getGroups(),
                    $this->exclusion->getSinceVersion(),
                    $this->exclusion->getUntilVersion(),
                    $this->exclusion->getMaxDepth(),
                    'expr(! object.getOffset())'
                )
            ),
        ];
    }
}
