<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Ibrows\RestBundle\Patch\RootPointer;
use PHPUnit_Framework_TestCase;

class RootPointerTest extends PHPUnit_Framework_TestCase
{
    public function testPublicInterface()
    {
        $pointer = RootPointer::create();

        $this->assertNull($pointer->lastToken());
        $this->assertNull($pointer->path());
    }
}
