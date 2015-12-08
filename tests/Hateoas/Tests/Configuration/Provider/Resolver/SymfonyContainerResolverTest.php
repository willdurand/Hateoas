<?php

namespace Hateoas\Tests\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\Resolver\SymfonyContainerResolver;
use Hateoas\Tests\TestCase;

class SymfonyContainerResolverTest extends TestCase
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

        $providerProvider = new SymfonyContainerResolver($containerProphecy->reveal());

        $this->assertNull($providerProvider->getRelationProvider(new RelationProviderConfiguration('!-;'), $object));
        $this->assertNull($providerProvider->getRelationProvider(new RelationProviderConfiguration('getSomething'), $object));
        $this->assertNull($providerProvider->getRelationProvider(new RelationProviderConfiguration('foo::bar'), $object));
        $this->assertSame(
            [$service, 'method'],
            $providerProvider->getRelationProvider(new RelationProviderConfiguration('acme.foo_service:method'), $object)
        );
    }
}
