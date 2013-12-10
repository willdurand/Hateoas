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

        $linkHelperMock = $this
            ->getMockBuilder('Hateoas\Helper\LinkHelper')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $linkHelperMock
            ->expects($this->once())
            ->method('getLinkHref')
            ->will($this->returnValue('/foo'))
            ->with($object, 'self', false)
        ;

        $expressionEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $expressionEvaluator->registerFunction(new LinkExpressionFunction($linkHelperMock));

        $this->assertEquals(
            '/foo',
            $expressionEvaluator->evaluate('expr(link(object, "self", false))', $object)
        );
    }
}
