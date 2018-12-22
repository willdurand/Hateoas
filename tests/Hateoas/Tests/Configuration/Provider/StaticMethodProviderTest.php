<?php

declare(strict_types=1);

namespace Hateoas\Tests\Configuration\Provider;

use Hateoas\Configuration\Provider\StaticMethodProvider;
use Hateoas\Configuration\Relation;
use Hateoas\Configuration\RelationProvider;
use Hateoas\Tests\TestCase;

class StaticMethodProviderTest extends TestCase
{
    public function test()
    {
        $providerProvider = new StaticMethodProvider();

        $this->assertEmpty($providerProvider->getRelations(new RelationProvider('!-;'), \stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProvider('getSomething'), \stdClass::class));
        $this->assertEmpty($providerProvider->getRelations(new RelationProvider('foo:bar'), \stdClass::class));

        $this->assertEquals(
            [new Relation('abcdef')],
            $providerProvider->getRelations(new RelationProvider(self::class . '::abc'), \stdClass::class)
        );
    }

    public static function abc()
    {
        return [new Relation('abcdef')];
    }
}
