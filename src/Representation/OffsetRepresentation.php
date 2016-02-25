<?php
namespace Ibrows\RestBundle\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\OffsetRepresentation as BaseOffsetRepresentation;

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
        array $parameters,
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
     * @return int
     * @codeCoverageIgnore
     */
    public function getLastPage()
    {
        return ($this->getTotal() - 1) - (($this->getTotal() - 1) % $this->getLimit());
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getPreviousPage()
    {
        return ($this->getOffset() > $this->getLimit())
            ? $this->getOffset() - $this->getLimit()
            : 0;
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
                    'expr(object.getParameters(1))',
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
                    'expr(object.getParameters(object.getLastPage())',
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
                    'expr(object.getParameters(object.getPreviousPage())',
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
