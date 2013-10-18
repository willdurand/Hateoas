<?php

namespace Hateoas\Tests\Configuration\Provider\Resolver;

use Hateoas\Configuration\RelationProvider;
use Hateoas\Configuration\Provider\Resolver\MethodResolver;
use Hateoas\Tests\TestCase;

class MethodResolverTest extends TestCase
{
    public function test()
    {
        $object = new \StdClass();
        $providerProvider = new MethodResolver();

        $this
            ->variable($providerProvider->getRelationProvider(new RelationProvider('!-;'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProvider('foo:bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProvider('foo::bar'), $object))
                ->isNull()
            ->variable($providerProvider->getRelationProvider(new RelationProvider('getRelations'), $object))
                ->isEqualTo(array($object, 'getRelations'))
        ;
    }
}
