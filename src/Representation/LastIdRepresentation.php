<?php
/**
 * Created by PhpStorm.
 * User: fabs
 * Date: 1/14/16
 * Time: 10:14 AM
 */

namespace Ibrows\RestBundle\Representation;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\AbstractSegmentedRepresentation;
use Hateoas\Representation\OffsetRepresentation as BaseOffsetRepresentation;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Class LastIdRepresentation
 * @package Ibrows\RestBundle\Representation
 * @Hateoas\RelationProvider("getRelations")
 */
class LastIdRepresentation extends AbstractSegmentedRepresentation
{

    /**
     * @var Exclusion
     */
    private $exclusion;

    /**
     * @var $lastId
     */
    protected $lastId;

    /**
     * @var string
     */
    protected $lastIdParamName;

    /**
     * @var string
     */
    protected $sortBy;

    /**
     * @var string
     */
    protected $sortByParamName;

    /**
     * @var string
     */
    protected $sortDir;

    /**
     * @var string
     */
    protected $sortDirParamName;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        $inline,
        $route,
        array $parameters = array(),
        $lastId,
        $lastIdParamName,
        $limit,
        $limitParameterName = null,
        $sortBy = null,
        $sortByParameterName = null,
        $sortDir = null,
        $sortDirParameterName = null,
        $absolute = false
    ) {
        parent::__construct(
            $inline,
            $route,
            $parameters,
            $limit,
            null,
            $limitParameterName,
            $absolute
        );

        $exclusion = null;
        $this->lastId = $lastId;
        $this->lastIdParamName = $lastIdParamName;
        $this->sortBy = $sortBy;
        $this->sortByParamName = $sortByParameterName;
        $this->sortDir = $sortDir;
        $this->sortDirParamName = $sortDirParameterName;

        if ($exclusion === null) {
            $exclusion = new Exclusion(
                [
                    'hateoas_list'
                ]
            );
        }

        $this->exclusion = $exclusion;
    }

    /**
     * @param null $limit
     * @param null $offsetId
     * @return array
     */
    public function getParameters($limit = null, $offsetId = null)
    {
        $params = array(
            $this->getLimitParameterName() => $limit ? $limit : $this->getLimit(),
            $this->sortByParamName         => $this->sortBy,
            $this->sortDirParamName        => $this->sortDir,
        );

        if ($offsetId !== false) {
            $params[$this->lastIdParamName] = $offsetId ? $offsetId : $this->lastId;
        }

        return $params;
    }


    /**
     * @param LastIdRepresentation   $object
     * @param ClassMetadataInterface $classMetadata
     * @return Relation[]
     * @codeCoverageIgnore
     */
    public function getRelations($object, ClassMetadataInterface $classMetadata)
    {
        return [
            new Relation(
                'next',
                new Route(
                    'expr(object.getRoute())',
                    'expr(object.getParameters())'
                ),
                null,
                [],
                $this->exclusion
            ),
            new Relation(
                'first',
                new Route(
                    'expr(object.getRoute())',
                    'expr(object.getParameters(null, false))'
                ),
                null,
                [],
                $this->exclusion
            ),

        ];
    }

}