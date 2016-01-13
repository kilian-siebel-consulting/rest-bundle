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
     * @param  array  $data
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

    public function evaluateArray(array $array, $data)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            $key   = $this->evaluate($key, $data);
            $value = is_array($value) ? $this->evaluateArray($value, $data) : $this->evaluate($value, $data);

            $newArray[$key] = $value;
        }

        return $newArray;
    }

    /**
     * Register a new new ExpressionLanguage function.
     *
     * @param ExpressionFunctionInterface $function
     *
     * @return ExpressionEvaluator
     */
    public function registerFunction(ExpressionFunctionInterface $function)
    {
        $this->expressionLanguage->register(
            $function->getName(),
            $function->getCompiler(),
            $function->getEvaluator()
        );

        foreach ($function->getContextVariables() as $name => $value) {
            $this->setContextVariable($name, $value);
        }

        return $this;
    }
}