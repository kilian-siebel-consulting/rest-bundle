<?php
namespace Ibrows\RestBundle\Tests\Expression;

use Ibrows\RestBundle\Expression\ExpressionEvaluator;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionEvaluatorTest extends PHPUnit_Framework_TestCase
{
    protected function getEvaluator()
    {
        return new ExpressionEvaluator(new ExpressionLanguage());
    }

    public function testNonExpression()
    {
        $this->assertEquals('foo', $this->getEvaluator()->evaluate('foo', []));
        $this->assertEquals([], $this->getEvaluator()->evaluate([], []));
        $this->assertEquals(42, $this->getEvaluator()->evaluate(42, []));
        $object = new stdClass();
        $this->assertEquals($object, $this->getEvaluator()->evaluate($object, []));
    }

    /**
     * @expectedException Symfony\Component\ExpressionLanguage\SyntaxError
     */
    public function testInvalidSyntax()
    {
        $this->getEvaluator()->evaluate('expr(/)', []);
    }

    /**
     * @expectedException Symfony\Component\ExpressionLanguage\SyntaxError
     */
    public function testInvalidVariable()
    {
        $this->getEvaluator()->evaluate('expr(foo)', []);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidMethod()
    {
        $this->getEvaluator()->evaluate('expr(foo.getMissing())', [
            'foo' => 'bar'
        ]);
    }

    public function testExpression()
    {
        $this->assertEquals('something', $this->getEvaluator()->evaluate('expr(foo.getSomething())', [
            'foo' => new TestClass()
        ]));
    }
}

class TestClass
{
    public function getSomething()
    {
        return 'something';
    }
}