<?php

namespace tests\Hateoas\Expression;

use Symfony\Component\ExpressionLanguage\Node\Node;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use tests\TestCase;
use Hateoas\Expression\ExpressionEvaluator as TestedExpressionEvaluator;

class ExpressionEvaluator extends TestCase
{
    public function testNullEvaluate()
    {
        $expressionLanguageMock = new \mock\Symfony\Component\ExpressionLanguage\ExpressionLanguage();
        $expressionEvaluator = new TestedExpressionEvaluator($expressionLanguageMock);

        $this
            ->string($expressionEvaluator->evaluate('hello', null))
                ->isEqualTo('hello')
            ->mock($expressionLanguageMock)
                ->call('parse')
                    ->never()
        ;
    }

    public function testEvaluate()
    {
        $parsedExpression = null;

        $expressionLanguageMock = new \mock\Symfony\Component\ExpressionLanguage\ExpressionLanguage();
        $expressionLanguageMock->getMockController()->evaluate = function () {
            return '42';
        };
        $expressionLanguageMock->getMockController()->parse = function () use (&$parsedExpression) {
            return $parsedExpression = new ParsedExpression('', new Node());
        };

        $expressionEvaluator = new TestedExpressionEvaluator($expressionLanguageMock);

        $data = new \StdClass();

        $this
            ->string($expressionEvaluator->evaluate('expr("42")', $data))
                ->isEqualTo('42')
            ->mock($expressionLanguageMock)
                ->call('parse')
                    ->withArguments('"42"')
                    ->once()
                ->call('evaluate')
                    ->withArguments($parsedExpression, array('object' => $data))
                    ->once()
        ;
    }

    public function testEvaluateArray()
    {
        $parsedExpressions = array();

        $expressionLanguageMock = new \mock\Symfony\Component\ExpressionLanguage\ExpressionLanguage();
        $expressionLanguageMock->getMockController()->evaluate = function ($expression) {
            return strlen($expression);
        };
        $expressionLanguageMock->getMockController()->parse = function ($expression) use (&$parsedExpressions, &$parsedCount) {
            return $parsedExpressions[] = new ParsedExpression($expression, new Node());
        };

        $expressionEvaluator = new TestedExpressionEvaluator($expressionLanguageMock);

        $data = new \StdClass();

        $array = array(
            'expr(a)' => 'expr(aa)',
            'hello' => 'expr(aaa)',
        );

        $this
            ->array($expressionEvaluator->evaluateArray($array, $data))
                ->isEqualTo(array(
                    1 => 2,
                    'hello' => 3,
                ))
            ->mock($expressionLanguageMock)
                ->call('parse')
                    ->withArguments('a')
                    ->once()
                ->call('parse')
                    ->withArguments('aa')
                    ->once()
                ->call('parse')
                    ->withArguments('aaa')
                    ->once()
                ->call('evaluate')
                    ->withArguments($parsedExpressions[0], array('object' => $data))
                    ->once()
                ->call('evaluate')
                    ->withArguments($parsedExpressions[1], array('object' => $data))
                    ->once()
                ->call('evaluate')
                    ->withArguments($parsedExpressions[2], array('object' => $data))
                    ->once()
        ;
    }

    public function testSetContextValue()
    {
        $parsedExpression = null;

        $expressionLanguageMock = new \mock\Symfony\Component\ExpressionLanguage\ExpressionLanguage();
        $expressionLanguageMock->getMockController()->evaluate = function ($expression, $values) {
            return $values[(string) $expression];
        };
        $expressionLanguageMock->getMockController()->parse = function ($expression) use (&$parsedExpression) {
            return $parsedExpression = new ParsedExpression($expression, new Node());
        };

        $expressionEvaluator = new TestedExpressionEvaluator($expressionLanguageMock);
        $expressionEvaluator->setContextValue('name', 'Adrien');

        $data = new \StdClass();

        $this
            ->string($expressionEvaluator->evaluate('expr(name)', $data))
                ->isEqualTo('Adrien')
            ->mock($expressionLanguageMock)
                ->call('parse')
                    ->withArguments('name')
                    ->once()
                ->call('evaluate')
                    ->withArguments($parsedExpression, array('object' => $data, 'name' => 'Adrien'))
                    ->once()
        ;
    }
}
