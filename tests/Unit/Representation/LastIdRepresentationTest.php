<?php
namespace Ibrows\RestBundle\Tests\Unit\Representation;

use Ibrows\RestBundle\Representation\CollectionRepresentation;
use Ibrows\RestBundle\Representation\LastIdRepresentation;
use PHPUnit_Framework_TestCase;

class LastIdRepresentationTest extends PHPUnit_Framework_TestCase
{
    public function testGetParameters()
    {
        $representation = new LastIdRepresentation(
            new CollectionRepresentation([]),
            [],
            [],
            42,
            'offsetId',
            7,
            'limit',
            'name',
            'sortBy',
            'asc',
            'sortDir'
        );
        $this->assertEquals(
            [
                'limit'    => 7,
                'sortBy'   => 'name',
                'sortDir'  => 'asc',
                'offsetId' => 42
            ],
            $representation->getParameters()
        );
    }
}
