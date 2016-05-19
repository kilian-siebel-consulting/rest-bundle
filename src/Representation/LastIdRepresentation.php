<?php

namespace Ibrows\RestBundle\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Route;
use Hateoas\Representation\AbstractSegmentedRepresentation;
use Ibrows\RestBundle\Model\ApiListableInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class LastIdRepresentation
 * @package Ibrows\RestBundle\Representation
 *
 * @Serializer\ExclusionPolicy("ALL")
 *
 * @Hateoas\Relation(
 *     "next",
 *     href = @Hateoas\Route(
 *         "expr(object.getRoute())",
 *         parameters = "expr(object.getParameters())",
 *     ),
 * )
 * @Hateoas\Relation(
 *     "first",
 *     href = @Hateoas\Route(
 *         "expr(object.getRoute())",
 *         parameters = "expr(object.getParameters(null, false))",
 *     ),
 * )
 */
class LastIdRepresentation extends AbstractSegmentedRepresentation
{
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

        $this->lastId = $lastId;
        $this->lastIdParamName = $lastIdParamName;
        $this->sortBy = $sortBy;
        $this->sortByParamName = $sortByParameterName;
        $this->sortDir = $sortDir;
        $this->sortDirParamName = $sortDirParameterName;
    }

    /**
     * @param null|int $limit
     * @param null|false|int $offsetId
     * @return array
     */
    public function getParameters($limit = null, $offsetId = null)
    {
        $parameters = parent::getParameters($limit);

        $params = [];

        if ($this->sortByParamName) {
            $params[$this->sortByParamName] = $this->sortBy;
        }

        if ($this->sortDirParamName) {
            $params[$this->sortDirParamName] = $this->sortDir;
        }

        if ($this->getLimitParameterName()) {
            $params[$this->getLimitParameterName()] = $limit ? $limit : $this->getLimit();
        }

        if ($offsetId === null && $this->lastId !== null) {
            $offsetId = $this->lastId;
        }

        if ($this->lastIdParamName && $offsetId !== null) {
            $params[$this->lastIdParamName] = $offsetId;
        }

        foreach ($parameters as $name => $param) {
            $params[$name] = $param;
        }

        return $params;
    }
}
