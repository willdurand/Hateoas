<?php

namespace tests\Hateoas\Serializer;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use Hateoas\Serializer\ExclusionManager as TestedExclusionManager;
use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\SerializationContext;
use tests\TestCase;

class ExclusionManager extends TestCase
{
    public function testDoesNotSkipNonNullEmbed()
    {
        $exclusionManager = new TestedExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipEmbed($object, $relation, $context))
                ->isFalse()
        ;
    }

    public function testSkipNullEmbed()
    {
        $exclusionManager = new TestedExclusionManager($this->mockExpressionEvaluator());

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipEmbed($object, $relation, $context))
                ->isTrue()
        ;
    }

    public function testDoesNotSkipNonNullLink()
    {
        $exclusionManager = new TestedExclusionManager($this->mockExpressionEvaluator());

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
        $exclusionManager = new TestedExclusionManager($this->mockExpressionEvaluator());

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
        $exclusionStrategyCallback = function (PropertyMetadata $property, Context $navigatorContext) use ($test) {
            $test
                ->array($property->groups)
                    ->isEqualTo(array('foo', 'bar'))
                ->float($property->sinceVersion)
                    ->isEqualTo(1.1)
                ->float($property->untilVersion)
                    ->isEqualTo(1.7)
                ->integer($property->maxDepth)
                    ->isEqualTo(77)
            ;
        };

        $exclusionManager = new TestedExclusionManager($this->mockExpressionEvaluator());
        $exclusionStrategy = $this->createExclusionStrategy(true, $exclusionStrategyCallback);

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
            ->boolean($exclusionManager->shouldSkipEmbed($object, $relation, $context))
                ->isTrue()
            ->mock($exclusionStrategy)
                ->call('shouldSkipProperty')
                    ->twice()
        ;
    }

    public function testSkipEmbed()
    {
        $exclusionManager = new TestedExclusionManager($this->mockExpressionEvaluator());

        $exclusionStrategy = new \mock\JMS\Serializer\Exclusion\ExclusionStrategyInterface();
        $exclusionStrategy->getMockController()->shouldSkipProperty = function () use (&$i) {
            return ++$i < 1 ? false : true;
        };

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create()
            ->addExclusionStrategy($exclusionStrategy)
        ;

        $this
            ->boolean($exclusionManager->shouldSkipEmbed($object, $relation, $context))
                ->isTrue()
        ;
    }

    /**
     * @dataProvider getTestSkipExcludeIfData
     */
    public function testSkipExcludeIf($exclude)
    {
        $expressionEvaluator = $this->mockExpressionEvaluator();
        $expressionEvaluator->getMockController()->evaluate = function () use ($exclude) {
            return $exclude;
        };
        $exclusionManager = new TestedExclusionManager($expressionEvaluator);

        $object = (object) array('name' => 'adrien');
        $exclusion = new Exclusion(null, null, null, null, 'expr(stuff)');
        $relation = new Relation('foo', 'foo', 'foo', array(), $exclusion);
        $context = SerializationContext::create();

        $this
            ->boolean($exclusionManager->shouldSkipLink($object, $relation, $context))
                ->isEqualTo($exclude)
            ->boolean($exclusionManager->shouldSkipEmbed($object, $relation, $context))
                ->isEqualTo($exclude)
            ->mock($expressionEvaluator)
                ->call('evaluate')
                    ->withArguments('expr(stuff)', $object)
        ;
    }

    public function getTestSkipExcludeIfData()
    {
        return array(
            array(true),
            array(false),
        );
    }

    private function createExclusionStrategy($shouldSkipProperty = false, $shouldSkipPropertyCallback = null)
    {
        $exclusionStrategy = new \mock\JMS\Serializer\Exclusion\ExclusionStrategyInterface();
        $exclusionStrategy->getMockController()->shouldSkipProperty = function () use ($shouldSkipProperty, $shouldSkipPropertyCallback) {
            if (null !== $shouldSkipPropertyCallback) {
                call_user_func_array($shouldSkipPropertyCallback, func_get_args());
            }

            return $shouldSkipProperty;
        };

        return $exclusionStrategy;
    }

    private function mockExpressionEvaluator()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\Hateoas\Expression\ExpressionEvaluator();
    }
}
