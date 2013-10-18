<?php

namespace Hateoas\Tests\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider;
use Hateoas\Configuration\Provider\Resolver\StaticMethodResolver;
use Hateoas\Tests\TestCase;

class StaticMethodResolverTest extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $providerProvider = new StaticMethodResolver();

        $this
            ->variable($providerProvider->getRelationProvider(new RelationProvider('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProvider('getSomething'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProvider('foo:bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProvider('Hateoas\Stuff::getRelations'), $object))
                ->isEqualTo(array('Hateoas\Stuff', 'getRelations'))
        ;
    }
}
