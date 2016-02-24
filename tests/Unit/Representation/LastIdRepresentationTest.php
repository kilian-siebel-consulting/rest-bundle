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
            7,
            'limit',
            42,
            'offsetId',
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

    public function testGetPartialParameters()
    {
        $representation = new LastIdRepresentation(
            new CollectionRepresentation([]),
            [],
            [],
            7,
            'limit',
            null,
            'offsetId',
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
            ],
            $representation->getParameters()
        );
    }


    public function testGetPartialLessParameters()
    {
        $representation = new LastIdRepresentation(
            new CollectionRepresentation([]),
            [],
            [],
            7,
            'limit'
        );

        $this->assertEquals(
            [
                'limit'    => 7,
            ],
            $representation->getParameters()
        );
    }
}
