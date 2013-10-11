<?php

namespace tests\Hateoas\Configuration\Provider;

use Hateoas\Configuration\RelationProvider as RelationProviderConfiguration;
use Hateoas\Configuration\Provider\MethodRelationProviderProvider as TestedMethodRelationProviderProvider;
use tests\TestCase;

class MethodRelationProviderProvider extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $providerProvider = new TestedMethodRelationProviderProvider();

        $this
            ->variable($providerProvider->get(new RelationProviderConfiguration('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->get(new RelationProviderConfiguration('foo:bar'), $object))
                ->isNull()
            ->variable($providerProvider->get(new RelationProviderConfiguration('foo::bar'), $object))
                ->isNull()
            ->variable($providerProvider->get(new RelationProviderConfiguration('getRelations'), $object))
                ->isEqualTo(array($object, 'getRelations'))
        ;
    }
}
