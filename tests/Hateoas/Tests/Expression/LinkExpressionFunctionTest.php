<?php

declare(strict_types=1);

namespace Hateoas\Tests\Expression;

use Hateoas\Expression\LinkExpressionFunction;
use Hateoas\Helper\LinkHelper;
use Hateoas\Tests\TestCase;
use JMS\Serializer\Expression\ExpressionEvaluator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LinkExpressionFunctionTest extends TestCase
{
    public function testEvaluate()
    {
        $object = new \StdClass();

        $linkHelperMock = $this->mockHelper('/foo', $object, 'self', false);

        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->registerProvider(new LinkExpressionFunction());

        $expressionEvaluator = new ExpressionEvaluator($expressionLanguage, ['link_helper' => $linkHelperMock]);

        $this->assertEquals(
            '/foo',
            $expressionEvaluator->evaluate('link(object, "self", false)', ['object' => $object])
        );
    }

    public function testCompile()
    {
        $object = new \StdClass();

        $linkHelperMock = $this->mockHelper('/foo', $object, 'self', false);

        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->registerProvider(new LinkExpressionFunction());

        $expressionEvaluator = new ExpressionEvaluator($expressionLanguage);

        $compiledExpression = $expressionLanguage->compile('link(object, "self", false)', ['object']);

        // setup variables for expression eval
        $link_helper = $linkHelperMock;

        $this->assertEquals('/foo', eval(sprintf('return %s;', $compiledExpression)));
    }

    private function mockHelper(string $result, \stdClass $expectedObject, string $expectedRel, bool $expectedAbsolute): LinkHelper
    {
        $linkHelperMock = $this
            ->getMockBuilder(LinkHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $linkHelperMock
            ->expects($this->once())
            ->method('getLinkHref')
            ->will($this->returnValue($result))
            ->with($expectedObject, $expectedRel, $expectedAbsolute);

        return $linkHelperMock;
    }
}
