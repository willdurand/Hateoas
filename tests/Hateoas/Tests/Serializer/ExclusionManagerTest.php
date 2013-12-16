<?php

namespace Hateoas\Tests\Serializer;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use Hateoas\Serializer\ExclusionManager;
use JMS\Serializer\Context;
use JMS\Serializer\SerializationContext;
use Hateoas\Tests\TestCase;

class ExclusionManagerTest extends TestCase
{
    public function testDoesNotSkipNonNullEmbedded()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipEmbedded($object, $relation, $context))
                ->isFalse()
        ;
    }

    public function testSkipNullEmbedded()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipEmbedded($object, $relation, $context))
                ->isTrue()
        ;
    }

    public function testDoesNotSkipNonNullLink()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipLink($object, $relation, $context))
                ->isFalse()
        ;
    }

    public function testSkipNullLink()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', null, 'foo');
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipLink($object, $relation, $context))
                ->isTrue()
        ;
    }

    public function testSkip()
    {
        $test = $this;
        $exclusionStrategyCallback = function ($args) use ($test) {
            $test
                ->array($args[0]->groups)
                    ->isEqualTo(array('foo', 'bar'))
                ->float($args[0]->sinceVersion)
                    ->isEqualTo(1.1)
                ->float($args[0]->untilVersion)
                    ->isEqualTo(1.7)
                ->integer($args[0]->maxDepth)
                    ->isEqualTo(77)
            ;
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

        $this
            ->boolean($exclusionManager->shouldSkipLink($object, $relation, $context))
                ->isTrue()
            ->boolean($exclusionManager->shouldSkipEmbedded($object, $relation, $context))
                ->isTrue()
        ;
    }

    public function testSkipEmbedded()
    {
        $exclusionManager = new ExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create()
            ->addExclusionStrategy($this->mockExclusionStrategy(true))
        ;

        $this
            ->boolean($exclusionManager->shouldSkipEmbedded($object, $relation, $context))
                ->isTrue()
        ;
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

        $this
            ->boolean($exclusionManager->shouldSkipLink($object, $relation, $context))
                ->isEqualTo($exclude)
            ->boolean($exclusionManager->shouldSkipEmbedded($object, $relation, $context))
                ->isEqualTo($exclude)
        ;
    }

    public function getTestSkipExcludeIfData()
    {
        return array(
            array(true),
            array(false),
        );
    }

    private function mockExclusionStrategy($shouldSkipProperty = false, $shouldSkipPropertyCallback = null, $calledTimes = null)
    {
        $exclusionStrategyProphecy = $this->prophesize('JMS\Serializer\Exclusion\ExclusionStrategyInterface');
        $method = $exclusionStrategyProphecy
            ->shouldSkipProperty(
                $this->arg->type('Hateoas\Serializer\Metadata\RelationPropertyMetadata'),
                $this->arg->type('JMS\Serializer\SerializationContext')
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
