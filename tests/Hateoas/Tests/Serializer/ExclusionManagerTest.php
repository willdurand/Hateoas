<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use Hateoas\Serializer\ExclusionManager;
use JMS\Serializer\Context;
use JMS\Serializer\SerializationContext;
use Hateoas\Tests\TestCase;
use Prophecy\Argument;

class ExclusionManagerTest extends TestCase
{
    public function testDoesNotSkipNonNullEmbedded()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create();

        $this->assertFalse($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testSkipNullEmbedded()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testDoesNotSkipNonNullLink()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this->assertFalse($exclusionManager->shouldSkipLink($object, $relation, $context));
    }

    public function testSkipNullLink()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', null, 'foo');
        $context = SerializationContext::create();

        $this->assertTrue($exclusionManager->shouldSkipLink($object, $relation, $context));
    }

    public function testSkip()
    {
        $test = $this;
        $exclusionStrategyCallback = function ($args) use ($test) {
            $test->assertSame(['foo', 'bar'], $args[0]->groups);
            $test->assertSame(1.1, $args[0]->sinceVersion);
            $test->assertSame(1.7, $args[0]->untilVersion);
            $test->assertSame(77, $args[0]->maxDepth);
        };

        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());
        $exclusionStrategy = $this->mockExclusionStrategy(true, $exclusionStrategyCallback, 2);

        $object = new \StdClass();
        $exclusion = new Exclusion(
            array('foo', 'bar'),
            1.1,
            1.7,
            77
        );
        $relation = new Relation('foo', 'foo', 'foo', array(), $exclusion);
        $context = SerializationContext::create()
            ->addExclusionStrategy($exclusionStrategy)
        ;

        $this->assertTrue($exclusionManager->shouldSkipLink($object, $relation, $context));
        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testSkipEmbedded()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create()
            ->addExclusionStrategy($this->mockExclusionStrategy(true))
        ;

        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    /**
     * @dataProvider getTestSkipExcludeIfData
     */
    public function testSkipExcludeIf($exclude)
    {
        $object = (object) array('name' => 'adrien');
        $exclusion = new Exclusion(null, null, null, null, 'expr(stuff)');
        $relation = new Relation('foo', 'foo', 'foo', array(), $exclusion);
        $context = SerializationContext::create();

        $expressionEvaluatorProphecy = $this->prophesizeExpressionEvaluator();
        $expressionEvaluatorProphecy
            ->evaluate('expr(stuff)', $object)
            ->willReturn($exclude)
        ;
        $exclusionManager = new ExclusionManager($expressionEvaluatorProphecy->reveal());

        $this->assertSame($exclude, $exclusionManager->shouldSkipLink($object, $relation, $context));
        $this->assertSame($exclude, $exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function getTestSkipExcludeIfData()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @param \Closure $shouldSkipPropertyCallback
     * @param integer $calledTimes
     */
    private function mockExclusionStrategy($shouldSkipProperty = false, $shouldSkipPropertyCallback = null, $calledTimes = null)
    {
        $exclusionStrategyProphecy = $this->prophesize('JMS\Serializer\Exclusion\ExclusionStrategyInterface');
        $method = $exclusionStrategyProphecy
            ->shouldSkipProperty(
                Argument::type('Hateoas\Serializer\Metadata\RelationPropertyMetadata'),
                Argument::type('JMS\Serializer\SerializationContext')
            )
            ->will(function () use ($shouldSkipProperty, $shouldSkipPropertyCallback) {
                if (null !== $shouldSkipPropertyCallback) {
                    call_user_func_array($shouldSkipPropertyCallback, func_get_args());
                }

                return $shouldSkipProperty;
            })
        ;

        if (null !== $calledTimes) {
            $method->shouldBeCalledTimes($calledTimes);
        }

        return $exclusionStrategyProphecy->reveal();
    }

    private function mockExpressionEvaluator()
    {
        return $this->prophesizeExpressionEvaluator()->reveal();
    }

    private function prophesizeExpressionEvaluator()
    {
        return $this->prophesize('Hateoas\Expression\ExpressionEvaluator');
    }
}
