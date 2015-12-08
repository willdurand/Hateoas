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

        $this->assertNull($providerProvider->getRelationProvider(new RelationProvider('!-;'), $object));
        $this->assertNull($providerProvider->getRelationProvider(new RelationProvider('getSomething'), $object));
        $this->assertNull($providerProvider->getRelationProvider(new RelationProvider('foo:bar'), $object));
        $this->assertSame(
            ['Hateoas\Stuff', 'getRelations'],
            $providerProvider->getRelationProvider(new RelationProvider('Hateoas\Stuff::getRelations'), $object)
        );
    }
}
