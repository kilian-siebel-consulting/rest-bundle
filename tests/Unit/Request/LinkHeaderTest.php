<?php
namespace Ibrows\RestBundle\Tests\Unit\Request;

use Ibrows\RestBundle\Request\LinkHeader;
use PHPUnit_Framework_TestCase;

class LinkHeaderTest extends PHPUnit_Framework_TestCase
{
    public function testInvalidValue()
    {
        $raw = 'invalid value';

        $link = new LinkHeader($raw);

        $this->assertEquals($raw, $link->getOriginalHeader());
    }

    public function testOnlyValue()
    {
        $raw = '<value>';

        $link = new LinkHeader($raw);

        $this->assertEquals($raw, $link->getOriginalHeader());
        $this->assertEquals('value', $link->getValue());
    }

    public function testWithAllOptions()
    {
        $raw = '<value>; rel="rel"; rev="rev"; title="title"; anchor="anchor"; something="some_value"';

        $link = new LinkHeader($raw);

        $this->assertEquals($raw, $link->getOriginalHeader());
        $this->assertEquals('value', $link->getValue());
        $this->assertEquals('rel', $link->getRelation());
        $this->assertEquals('rev', $link->getReverseRelation());
        $this->assertEquals('title', $link->getTitle());
        $this->assertEquals('anchor', $link->getAnchor());
        $this->assertEquals(1, count($link->getExtensions()));
        $this->assertEquals([ 'something' => 'some_value' ], $link->getExtensions());
        $this->assertEquals('some_value', $link->getExtension('something'));
    }

    public function testUnsetExtension()
    {
        $raw = '<value>';

        $link = new LinkHeader($raw);

        $this->assertEquals(null, $link->getExtension('something'));
    }

    public function testAccessors()
    {
        $link = new LinkHeader('value');

        $urlParameters = [
            'foo' => 'bar',
        ];
        $link->setUrlParameters($urlParameters);

        $resource = new \stdClass();
        $link->setResource($resource);

        $this->assertEquals($urlParameters, $link->getUrlParameters());
        $this->assertEquals($resource, $link->getResource());
    }
}
