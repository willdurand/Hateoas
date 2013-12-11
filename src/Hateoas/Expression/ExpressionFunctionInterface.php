<?php

namespace Hateoas\Expression;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface ExpressionFunctionInterface
{
    /**
     * Return the name of the function in an expression.
     *
     * @return string
     */
    public function getName();

    /**
     * Return a function executed when compiling an expression using the function.
     *
     * @return closure
     */
    public function getCompiler();

    /**
     * Return a function executed when the expression is evaluated.
     *
     * @return closure
     */
    public function getEvaluator();

    /**
     * Return context variables as an array.
     *
     * @return array
     */
    public function getContextVariables();
}
