<?php

namespace Hateoas\Tests\Expression;

use Symfony\Component\ExpressionLanguage\Node\Node;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use Hateoas\Tests\TestCase;
use Hateoas\Expression\ExpressionEvaluator;

class ExpressionEvaluatorTest extends TestCase
{
    public function testNullEvaluate()
    {
        $expressionLanguageProphecy = $this->prophesize('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
        $expressionLanguageProphecy
            ->parse($this->arg->any())
            ->shouldNotBeCalled()
        ;
        $expressionEvaluator = new ExpressionEvaluator($expressionLanguageProphecy->reveal());

        $this
            ->string($expressionEvaluator->evaluate('hello', null))
                ->isEqualTo('hello')
        ;
    }

    public function testEvaluate()
    {
        $data = new \StdClass();

        $expressionLanguageProphecy = $this->prophesize('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
        $expressionLanguageProphecy
            ->parse('"42"', array('object'))
            ->willReturn($parsedExpression = new ParsedExpression('', new Node()))
        ;
        $expressionLanguageProphecy
            ->evaluate($parsedExpression, array('object' => $data))
            ->willReturn('42')
        ;

        $expressionEvaluator = new ExpressionEvaluator($expressionLanguageProphecy->reveal());

        $this
            ->string($expressionEvaluator->evaluate('expr("42")', $data))
                ->isEqualTo('42')
        ;
    }

    public function testEvaluateArray()
    {
        $parsedExpressions = array(
            new ParsedExpression('a', new Node()),
            new ParsedExpression('aa', new Node()),
            new ParsedExpression('aaa', new Node()),
        );
        $data = new \StdClass();

        $ELProphecy = $this->prophesize('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
        $ELProphecy->parse('a', array('object'))->willReturn($parsedExpressions[0])->shouldBeCalledTimes(1);
        $ELProphecy->parse('aa', array('object'))->willReturn($parsedExpressions[1])->shouldBeCalledTimes(1);
        $ELProphecy->parse('aaa', array('object'))->willReturn($parsedExpressions[2])->shouldBeCalledTimes(1);

        $ELProphecy->evaluate($parsedExpressions[0], array('object' => $data))->willReturn(1);
        $ELProphecy->evaluate($parsedExpressions[1], array('object' => $data))->willReturn(2);
        $ELProphecy->evaluate($parsedExpressions[2], array('object' => $data))->willReturn(3);

        $expressionEvaluator = new ExpressionEvaluator($ELProphecy->reveal());

        $array = array(
            'expr(a)' => 'expr(aa)',
            'hello' => array('expr(aaa)'),
        );

        $this
            ->array($expressionEvaluator->evaluateArray($array, $data))
                ->isEqualTo(array(
                    1 => 2,
                    'hello' => array(3),
                ))
        ;
    }

    public function testSetContextValue()
    {
        $data = new \StdClass();

        $expressionLanguageProphecy = $this->prophesize('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
        $expressionLanguageProphecy
            ->parse('name', array('name', 'object'))
            ->willReturn($parsedExpression = new ParsedExpression('', new Node()))
            ->shouldBeCalledTimes(1)
        ;
        $expressionLanguageProphecy
            ->evaluate($parsedExpression, array('object' => $data, 'name' => 'Adrien'))
            ->willReturn('Adrien')
            ->shouldBeCalledTimes(1)
        ;

        $expressionEvaluator = new ExpressionEvaluator($expressionLanguageProphecy->reveal());
        $expressionEvaluator->setContextValue('name', 'Adrien');

        $this
            ->string($expressionEvaluator->evaluate('expr(name)', $data))
                ->isEqualTo('Adrien')
        ;
    }
}
