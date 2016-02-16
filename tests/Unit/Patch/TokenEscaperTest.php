<?php
namespace Ibrows\RestBundle\Tests\Unit\Patch;

use Ibrows\RestBundle\Patch\TokenEscapeInterface;
use Ibrows\RestBundle\Patch\TokenEscaper;
use PHPUnit_Framework_TestCase;

class TokenEscaperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TokenEscapeInterface
     */
    private $escaper;

    public function setUp()
    {
        $this->escaper = new TokenEscaper();
    }

    /**
     * @param string $token
     * @param string $expected
     * @dataProvider getTokens
     */
    public function testTokens($token, $expected)
    {
        $unescaped = $this->escaper->unescape($token);
        $this->assertEquals($expected, $unescaped);
        $this->assertEquals($token, $this->escaper->escape($unescaped));

    }

    /**
     * @return string[][]
     */
    public function getTokens()
    {
        return [
            [
                'value',
                'value',
            ],
            [
                'path~0with~0tildes',
                'path~with~tildes',
            ],
            [
                'path~1with~1slashes',
                'path/with/slashes',
            ],
        ];
    }
}
