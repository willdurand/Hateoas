<?php

namespace Hateoas\Tests\Configuration\Provider;

use Hateoas\Configuration\Provider\ExpressionEvaluatorProvider;
use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Tests\TestCase;

class ExpressionEvaluatorProviderTest extends TestCase
{
    public function test()
    {

        $object = new \StdClass();
        $service = new \StdClass();

        $containerProphecy = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerProphecy
            ->get('acme.foo_service')
            ->willReturn($service)
        ;

        $providerProvider = new ExpressionEvaluatorProvider($containerProphecy->reveal());

        $this->assertNull($providerProvider->getRelations(new RelationProviderConfiguration('!-;'), $object));
        $this->assertNull($providerProvider->getRelations(new RelationProviderConfiguration('getSomething'), $object));
        $this->assertNull($providerProvider->getRelations(new RelationProviderConfiguration('foo::bar'), $object));
        $this->assertSame(
            [$service, 'method'],
            $providerProvider->getRelations(new RelationProviderConfiguration('acme.foo_service:method'), $object)
        );
    }
}
