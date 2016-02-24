<?php

namespace Ibrows\RestBundle\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\AbstractSegmentedRepresentation;
use Ibrows\RestBundle\Model\ApiListableInterface;

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
     * @var
     */
    protected $parameters;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        $inline,
        $route,
        array $parameters,
        $limit,
        $limitParameterName = null,
        $lastId = null,
        $lastIdParamName = null,
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

        $this->parameters = $parameters;
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
     * @param null|int $limit
     * @param null|false|int $offsetId
     * @return array
     */
    public function getParameters($limit = null, $offsetId = null)
    {
        $params = [];

        if($this->sortByParamName){
            $params[$this->sortByParamName] = $this->sortBy;
        }

        if($this->sortDirParamName){
            $params[$this->sortDirParamName] = $this->sortDir;
        }

        if($this->getLimitParameterName()){
            $params[$this->getLimitParameterName()] = $limit ? $limit : $this->getLimit();
        }

        if ($offsetId === null && $this->lastId !== null) {
            $offsetId = $this->lastId;
        }

        if($this->lastIdParamName && $offsetId !== null) {
            $params[$this->lastIdParamName] = $offsetId;
        }

        foreach ($this->parameters as $name => $param) {
            if ($param instanceof ApiListableInterface) {
                $params[$name] = $param->getId();
            }
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
