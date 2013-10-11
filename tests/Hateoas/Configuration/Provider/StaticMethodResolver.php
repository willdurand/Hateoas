<?php

namespace tests\Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\StaticMethodResolver as TestedStaticMethodResolver;
use tests\TestCase;

class StaticMethodResolver extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $providerProvider = new TestedStaticMethodResolver();

        $this
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('getSomething'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('foo:bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProviderConfiguration('Hateoas\Stuff::getRelations'), $object))
                ->isEqualTo(array('Hateoas\Stuff', 'getRelations'))
        ;
    }
}
