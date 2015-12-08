<?php

namespace Hateoas\Tests\Expression;

use Prophecy\Argument;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Node\Node;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use Hateoas\Tests\TestCase;
use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Expression\ExpressionFunctionInterface;

class ExpressionEvaluatorTest extends TestCase
{
    public function testNullEvaluate()
    {
        $expressionLanguageProphecy = $this->prophesize('Symfony\Component\ExpressionLanguage\ExpressionLanguage');
        $expressionLanguageProphecy
            ->parse(Argument::any())
            ->shouldNotBeCalled()
        ;
        $expressionEvaluator = new ExpressionEvaluator($expressionLanguageProphecy->reveal());

        $this->assertSame('hello', $expressionEvaluator->evaluate('hello', null));
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

        $this->assertSame('42', $expressionEvaluator->evaluate('expr("42")', $data));
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

        $this->assertSame(
            $expressionEvaluator->evaluateArray($array, $data),
            [
                1 => 2,
                'hello' => [3],
            ]
        );
    }

    public function testSetContextVariable()
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
        $expressionEvaluator->setContextVariable('name', 'Adrien');

        $this->assertSame('Adrien', $expressionEvaluator->evaluate('expr(name)', $data));
    }

    public function testRegisterFunction()
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $expressionEvaluator->registerFunction(new HelloExpressionFunction());

        $this->assertSame('Hello, toto!', $expressionEvaluator->evaluate('expr(hello("toto"))', null));
    }

    /**
     * @dataProvider getTestEvaluateNonStringData
     */
    public function testEvaluateNonString($value)
    {
        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());

        $this->assertSame($value, $expressionEvaluator->evaluate($value, array()));
    }

    public function getTestEvaluateNonStringData()
    {
        return array(
            array(true),
            array(1.0),
            array(new \StdClass),
            array(array('foo' => 'bar')),
        );
    }
}

class HelloExpressionFunction implements ExpressionFunctionInterface
{
    public function getName()
    {
        return 'hello';
    }

    public function getCompiler()
    {
        return function ($value) {
            return sprintf('$hello_helper->hello(%s)', $value);
        };
    }

    public function getEvaluator()
    {
        return function (array $context, $value) {
            return $context['hello_helper']->hello($value);
        };
    }

    public function getContextVariables()
    {
        return array('hello_helper' => $this);
    }

    public function hello($name)
    {
        return sprintf('Hello, %s!', $name);
    }
}
