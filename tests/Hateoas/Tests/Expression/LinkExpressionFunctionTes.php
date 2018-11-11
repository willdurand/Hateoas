<?php

namespace Hateoas\Tests\Expression;

use Hateoas\Expression\ExpressionEvaluator;
use Hateoas\Expression\LinkExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkExpressionFunctionTest extends \PHPUnit_Framework_TestCase
{
    public function testEvaluate()
    {
        $object = new \StdClass();

        $linkHelperMock = $this->mockHelper('/foo', $object, 'self', false);

        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $expressionEvaluator->registerFunction(new LinkExpressionFunction($linkHelperMock));

        $this->assertEquals(
            '/foo',
            $expressionEvaluator->evaluate('expr(link(object, "self", false))', $object)
        );
    }

    public function testCompile()
    {
        $object = new \StdClass();

        $linkHelperMock = $this->mockHelper('/foo', $object, 'self', false);

        $expressionLanguage = new ExpressionLanguage();
        $expressionEvaluator = new ExpressionEvaluator($expressionLanguage);
        $expressionEvaluator->registerFunction(new LinkExpressionFunction($linkHelperMock));

        $compiledExpression = $expressionLanguage->compile('link(object, "self", false)', array('object', 'link_helper'));

        // setup variables for expression eval
        $object = $object;
        $link_helper = $linkHelperMock;

        $this->assertEquals('/foo', eval(sprintf('return %s;', $compiledExpression)));
    }

    /**
     * @param string $result
     * @param \stdClass $expectedObject
     * @param string $expectedRel
     * @param boolean $expectedAbsolute
     *
     * @return \Hateoas\Helper\LinkHelper
     */
    private function mockHelper($result, $expectedObject, $expectedRel, $expectedAbsolute)
    {
        $linkHelperMock = $this
            ->getMockBuilder('Hateoas\Helper\LinkHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $linkHelperMock
            ->expects($this->once())
            ->method('getLinkHref')
            ->will($this->returnValue('/foo'))
            ->with($expectedObject, $expectedRel, $expectedAbsolute)
        ;

        return $linkHelperMock;
    }
}
