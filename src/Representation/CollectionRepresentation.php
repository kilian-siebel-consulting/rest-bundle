<?php
namespace Ibrows\RestBundle\Representation;

use Hateoas\Configuration\Exclusion;
use Hateoas\Representation\CollectionRepresentation as BaseCollectionRepresentation;

class CollectionRepresentation extends BaseCollectionRepresentation
{
    /**
     * CollectionRepresentation constructor.
     *
     * @param                $resources
     * @param null           $rel
     * @param null           $xmlElementName
     * @param Exclusion|null $exclusion
     * @param Exclusion|null $embedExclusion
     * @param array          $relations
     */
    public function __construct(
        $resources,
        $rel = null,
        $xmlElementName = null,
        Exclusion $exclusion = null,
        Exclusion $embedExclusion = null,
        array $relations = []
    ) {
        if($exclusion === null) {
            $exclusion = new Exclusion([
                'hateoas_list',
            ]);
        }
        if($embedExclusion === null) {
            $embedExclusion = new Exclusion([
                'hateoas_list',
            ]);
        }

        parent::__construct($resources, $rel, $xmlElementName, $exclusion, $embedExclusion, $relations);
    }
}