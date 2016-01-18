<?php
namespace Ibrows\RestBundle\Expression;

use Hateoas\Expression\ExpressionFunctionInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionEvaluator
{
    const EXPRESSION_REGEX = '/expr\((?P<expression>.+)\)/';

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var array
     */
    private $cache;

    public function __construct(ExpressionLanguage $expressionLanguage)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->cache = array();
    }

    /**
     * @param  string $expression
     * @param  array  $context
     * @return mixed
     */
    public function evaluate($expression, $context)
    {
        if (!is_string($expression)) {
            return $expression;
        }

        $key = $expression;

        if (!array_key_exists($key, $this->cache)) {
            if (!preg_match(self::EXPRESSION_REGEX, $expression, $matches)) {
                $this->cache[$key] = false;
            } else {
                $expression = $matches['expression'];
                $this->cache[$key] = $this->expressionLanguage->parse($expression, array_keys($context));
            }
        }

        if (false !== $this->cache[$key]) {
            return $this->expressionLanguage->evaluate($this->cache[$key], $context);
        }

        return $expression;
    }
}