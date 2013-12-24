<?php

namespace Hateoas\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 * @author William Durand <william.durand1@gmail.com>
 */
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
    private $context;

    public function __construct(ExpressionLanguage $expressionLanguage, array $context = array())
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->context            = $context;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setContextVariable($name, $value)
    {
        $this->context[$name] = $value;
    }

    /**
     * @param  string $expression
     * @param  mixed  $data
     * @return mixed
     */
    public function evaluate($expression, $data)
    {
        if (!preg_match(self::EXPRESSION_REGEX, $expression, $matches)) {
            return $expression;
        }

        $expression = $matches['expression'];

        $context = array_merge($this->context, array(
            'object' => $data,
        ));

        $parsedExpression = $this->expressionLanguage->parse($expression, array_keys($context));

        return $this->expressionLanguage->evaluate($parsedExpression, $context);
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
