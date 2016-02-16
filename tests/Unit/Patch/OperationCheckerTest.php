<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Ibrows\RestBundle\Patch\OperationChecker;
use PHPUnit_Framework_TestCase;

class OperationCheckerTest extends PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $checker = new OperationChecker();
        $checker->validate(
            [
                'op'   => 'something',
                'path' => 'a path',
            ]
        );
    }

    /**
     * @expectedExceptionMessage The property "path" must be provided for every operation.
     * @expectedException \Ibrows\RestBundle\Patch\Exception\OperationInvalidException
     */
    public function testMissingPath()
    {
        $checker = new OperationChecker();
        $checker->validate(
            [
                'op' => 'something',
            ]
        );
    }

    /**
     * @expectedExceptionMessage The property "op" must be provided for every operation.
     * @expectedException \Ibrows\RestBundle\Patch\Exception\OperationInvalidException
     */
    public function testMissingOperation()
    {
        $checker = new OperationChecker();
        $checker->validate(
            [
                'path' => 'a path',
            ]
        );
    }
}
