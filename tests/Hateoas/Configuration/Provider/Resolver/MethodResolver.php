<?php

namespace tests\Hateoas\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\Resolver\MethodResolver as TestedMethodResolver;
use tests\TestCase;

class MethodResolver extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $providerProvider = new TestedMethodResolver();

        $this
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('foo:bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('foo::bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('getRelations'), $object))
                ->isEqualTo(array($object, 'getRelations'))
        ;
    }
}
