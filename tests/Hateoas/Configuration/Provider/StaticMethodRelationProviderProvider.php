<?php

namespace tests\Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\StaticMethodRelationProviderProvider as TestedStaticMethodRelationProviderProvider;
use tests\TestCase;

class StaticMethodRelationProviderProvider extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $providerProvider = new TestedStaticMethodRelationProviderProvider();

        $this
            ->variable($providerProvider->get(new RelationProviderConfiguration('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->get(new RelationProviderConfiguration('getSomething'), $object))
                ->isNull()
            ->variable($providerProvider->get(new RelationProviderConfiguration('foo:bar'), $object))
                ->isNull()
            ->variable($providerProvider->get(new RelationProviderConfiguration('Hateoas\Stuff::getRelations'), $object))
                ->isEqualTo(array('Hateoas\Stuff', 'getRelations'))
        ;
    }
}
