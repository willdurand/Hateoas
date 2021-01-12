<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Provider;

use Hateoas\Configuration\Provider\ExpressionEvaluatorProvider;
use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Tests\TestCase;
use JMS\Serializer\Expression\ExpressionEvaluatorInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class ExpressionEvaluatorProviderTest extends TestCase
{
    use ProphecyTrait;

    public function test()
    {
        $service = new \StdClass();

        $containerProphecy = $this->prophesize(ExpressionEvaluatorInterface::class);
        $containerProphecy
            ->evaluate('foo', ['class' => \stdClass::class])
            ->willReturn([$service]);

        $providerProvider = new ExpressionEvaluatorProvider($containerProphecy->reveal());

        $this->assertEmpty($providerProvider->getRelations(new RelationProviderConfiguration('!-;'), \stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProviderConfiguration('getSomething'), \stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProviderConfiguration('foo::bar'), \stdClass::class));
        $this->assertSame(
            [$service],
            $providerProvider->getRelations(new RelationProviderConfiguration('expr(foo)'), \stdClass::class)
        );
    }
}
