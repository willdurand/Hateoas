<?php

declare(strict_types=1);

namespace Hateoas\Tests\Serializer;

use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Relation;
use Hateoas\Serializer\ExclusionManager;
use Hateoas\Tests\TestCase;
use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExclusionManagerTest extends TestCase
{
    use ProphecyTrait;

    public function testDoesNotSkipNonNullEmbedded()
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create();

        $this->assertFalse($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testSkipNullEmbedded()
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testDoesNotSkipNonNullLink()
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo');
        $context = SerializationContext::create();

        $this->assertFalse($exclusionManager->shouldSkipLink($object, $relation, $context));
    }

    public function testSkipNullLink()
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

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
            $test->assertSame('1.1', $args[0]->sinceVersion);
            $test->assertSame('1.7', $args[0]->untilVersion);
            $test->assertSame(77, $args[0]->maxDepth);
        };

        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));
        $exclusionStrategy = $this->mockExclusionStrategy(true, $exclusionStrategyCallback, 2);

        $object = new \StdClass();
        $exclusion = new Exclusion(
            ['foo', 'bar'],
            '1.1',
            '1.7',
            77
        );
        $relation = new Relation('foo', 'foo', 'foo', [], $exclusion);
        $context = SerializationContext::create()
            ->addExclusionStrategy($exclusionStrategy);

        $this->assertTrue($exclusionManager->shouldSkipLink($object, $relation, $context));
        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public function testSkipEmbedded()
    {
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy(new ExpressionEvaluator(new ExpressionLanguage())));

        $object = new \StdClass();
        $relation = new Relation('foo', 'foo', 'foo');
        $context = SerializationContext::create()
            ->addExclusionStrategy($this->mockExclusionStrategy(true));

        $this->assertTrue($exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    #[DataProvider('getTestSkipExcludeIfData')]
    public function testSkipExcludeIf($exclude)
    {
        $object = (object) ['name' => 'adrien'];
        $exclusion = new Exclusion(null, null, null, null, 'stuff');
        $relation = new Relation('foo', 'foo', 'foo', [], $exclusion);
        $context = SerializationContext::create();
        $context->initialize(
            'json',
            $this->prophesize(SerializationVisitorInterface::class)->reveal(),
            $this->prophesize(GraphNavigatorInterface::class)->reveal(),
            $this->prophesize(MetadataFactoryInterface::class)->reveal()
        );

        $context->startVisiting($object);

        $expressionEvaluatorProphecy = $this->prophesize(ExpressionEvaluatorInterface::class);
        $expressionEvaluatorProphecy
            ->evaluate('stuff', Argument::any())
            ->shouldBeCalled()
            ->willReturn($exclude);
        $exclusionManager = new ExclusionManager(new ExpressionLanguageExclusionStrategy($expressionEvaluatorProphecy->reveal()));

        $this->assertSame($exclude, $exclusionManager->shouldSkipLink($object, $relation, $context));
        $this->assertSame($exclude, $exclusionManager->shouldSkipEmbedded($object, $relation, $context));
    }

    public static function getTestSkipExcludeIfData(): iterable
    {
        yield [true];
        yield [false];
    }

    /**
     * @param \Closure $shouldSkipPropertyCallback
     * @param int $calledTimes
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
            });

        if (null !== $calledTimes) {
            $method->shouldBeCalledTimes($calledTimes);
        }

        return $exclusionStrategyProphecy->reveal();
    }
}
