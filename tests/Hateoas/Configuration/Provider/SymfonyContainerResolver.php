<?php

namespace tests\Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\SymfonyContainerResolver as TestedSymfonyContainerResolver;
use tests\TestCase;

class SymfonyContainerResolver extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $service = new \StdClass();
        $containerMock = new \mock\Symfony\Component\DependencyInjection\ContainerInterface();
        $containerMock->getMockController()->get = function () use ($service) {
            return $service;
        };
        $providerProvider = new TestedSymfonyContainerResolver($containerMock);

        $this
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('getSomething'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('foo::bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('acme.foo_service:method'), $object))
                ->isEqualTo(array($service, 'method'))
            ->mock($containerMock)
                ->call('get')
                    ->withArguments('acme.foo_service')
        ;
    }
}
